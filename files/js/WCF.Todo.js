/**
 * Todo-related classes.
 * 
 * @author		Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	exclusive
 */

/**
 * Todo namespace
 */
WCF.Todo = {};

/**
 * Provides a generic update handler for todos.
 */
WCF.Todo.UpdateHandler = Class.extend({
	/**
	 * list of todos
	 * @var	object
	 */
	_todos: { },

	/**
	 * Initializes the todo update handler.
	 */
	init: function() {
		$('.todo').each($.proxy(function(index, todo) {
			var $todo = $(todo);

			this._todos[$todo.data('todoID')] = $todo;
		}, this));
	},

	/**
	 * Updates a set of properties for given todo id.
	 *
	 * @param	integer		todoID
	 * @param	object		data
	 */
	update: function(todoID, data) {
		if (!this._todos[todoID]) {
			return;
		}

		for (var $property in data) {
			this._updateProperty(todoID, $property, data[$property]);
		}
	},

	/**
	 * Wrapper for property updating.
	 *
	 * @param	integer		todoID
	 * @param	string		property
	 * @param	mixed		value
	 */
	_updateProperty: function(todoID, property, value) {
		switch (property) {
			case 'isDisabled':
				if (value) {
					this._disable(todoID);
				}
				else {
					this._enable(todoID);
				}
				break;

			case 'deleted':
				this._delete(todoID, value);
				break;

			case 'deleteNote':
				this._deleteNote(todoID, value);
				break;

			case 'isDeleted':
				if (value) {
					this._trash(todoID);
				}
				else {
					this._restore(todoID);
				}
				break;

			default:
				this._handleCustomProperty(todoID, property, value);
				break;
		}

		WCF.Clipboard.reload();
	},

	/**
	 * Handles custom properties not known to _updateProperty(), override if necessary.
	 *
	 * @param	integer		todoID
	 * @param	string		property
	 * @param	mixed		value
	 */
	_handleCustomProperty: function(todoID, property, value) {
		this._todos[todoID].trigger('todoUpdateHandlerProperty', [ todoID, property, value ]);
	},

	/**
	 * Enables a todo.
	 *
	 * @param	integer		todoID
	 */
	_enable: function(todoID) {
		this._todos[todoID].data('isDisabled', 0);
	},

	/**
	 * Disables a todo.
	 *
	 * @param	integer		todoID
	 */
	_disable: function(todoID) {
		this._todos[todoID].data('isDisabled', 1);
	},

	/**
	 * Trashes a todo.
	 *
	 * @param	integer		todoID
	 */
	_trash: function(todoID) {
		this._todos[todoID].data('isDeleted', 1);
	},

	/**
	 * Deletes a todo.
	 *
	 * @param	integer		todoID
	 * @param	string		categoryLink
	 */
	_delete: function(todoID, categoryLink) { },

	/**
	 * Displays the delete notice.
	 *
	 * @param	integer		todoID
	 * @param	string		message
	 */
	_deleteNote: function(todoID, reason) { },

	/**
	 * Restores a todo.
	 *
	 * @param	integer		todoID
	 */
	_restore: function(todoID) {
		this._todos[todoID].data('isDeleted', 0);
	},

	/**
	 * Returns generic property values for a todo.
	 *
	 * @param	integer		todoID
	 * @param	string		property
	 * @return	boolean
	 */
	getValue: function(todoID, property) {
		if (!this._todos[todoID]) {
			console.debug("[WCF.Todo.UpdateHandler] Unknown todo id " + todoID);
			return;
		}

		switch (property) {
			case 'isDisabled':
			case 'isDeleted':
				return this._todos[todoID].data(property);
				break;
		}
	}
});

/**
 * Inline editor for todos.
 *
 * @param	string		elementSelector
 */
WCF.Todo.InlineEditor = WCF.InlineEditor.extend({
	/**
	 * current editor environment
	 * @var	string
	 */
	_environment: 'todo',

	/**
	 * list of permissions
	 * @var	object
	 */
	_permissions: { },

	/**
	 * todo update handler
	 * @var	WCF.Todo.UpdateHandler
	 */
	_updateHandler: null,

	/**
	 * @see	WCF.InlineEditor._setOptions()
	 */
	_setOptions: function() {
		this._environment = 'todo';

		this._options = [
			// isDisabled
			{ label: WCF.Language.get('wcf.todo.edit.enable'), optionName: 'enable' },
			{ label: WCF.Language.get('wcf.todo.edit.disable'), optionName: 'disable' },

			// isDeleted
			{ label: WCF.Language.get('wcf.todo.edit.trash'), optionName: 'trash' },
			{ label: WCF.Language.get('wcf.todo.restore'), optionName: 'restore' },
			{ label: WCF.Language.get('wcf.todo.delete'), optionName: 'delete' },

			// divider
			{ optionName: 'divider' },

			// edit todo
			{ label: WCF.Language.get('wcf.todo.edit'), optionName: 'edit', isQuickOption: true }
		];
	},

	/**
	 * Returns current update handler.
	 *
	 * @return	WCF.Todo.UpdateHandler
	 */
	setUpdateHandler: function(updateHandler) {
		this._updateHandler = updateHandler;
	},

	/**
	 * @see	WCF.InlineEditor._getTriggerElement()
	 */
	_getTriggerElement: function(element) {
		return element.find('.jsTodoInlineEditor');
	},

	/**
	 * @see	WCF.InlineEditor._show()
	 */
	_show: function(event) {
		var $elementID = $(event.currentTarget).data('elementID');

		// build dropdown
		var $trigger = null;
		if (!this._dropdowns[$elementID]) {
			$trigger = this._getTriggerElement(this._elements[$elementID]).addClass('dropdownToggle');
			var $li = $trigger.parent().addClass('dropdown');
			this._dropdowns[$elementID] = $('<ul class="dropdownMenu" />').insertAfter($trigger);

			// force message options to stay visible while dropdown menu is shown
			WCF.Dropdown.registerCallback($li.wcfIdentify(), $.proxy(function(containerID, action) {
				WCF.Dropdown.getDropdown(containerID).parents('.messageOptions').toggleClass('forceOpen');
			}, this));
		}

		// manage list items
		this._super(event);

		// init dropdown
		if ($trigger !== null) {
			WCF.Dropdown.initDropdown($trigger, true);
		}

		return false;
	},

	/**
	 * @see	WCF.InlineEditor._validate()
	 */
	_validate: function(elementID, optionName) {
		var $todoID = $('#' + elementID).data('todoID');

		switch (optionName) {
			// isDisabled
			case 'enable':
				if (!this._getPermission('canEnableTodo')) {
					return false;
				}

				if (this._updateHandler.getValue($todoID, 'isDeleted')) {
					return false;
				}

				if (!this._updateHandler.getValue($todoID, 'isDisabled')) {
					return false;
				}

				return true;
				break;

			case 'disable':
				if (!this._getPermission('canEnableTodo')) {
					return false;
				}

				if (this._updateHandler.getValue($todoID, 'isDeleted')) {
					return false;
				}

				if (this._updateHandler.getValue($todoID, 'isDisabled')) {
					return false;
				}

				return true;
				break;

			// isDeleted
			case 'trash':
				if (!this._getPermission('canDeleteTodo')) {
					return false;
				}

				if (this._updateHandler.getValue($todoID, 'isDeleted')) {
					return false
				}

				return true;
				break;

			case 'delete':
				if (!this._getPermission('canDeleteTodoCompletely')) {
					return false;
				}

				if (!this._updateHandler.getValue($todoID, 'isDeleted')) {
					return false;
				}

				return true;
				break;

			case 'restore':
				if (!this._getPermission('canRestoreTodo')) {
					return false;
				}

				if (!this._updateHandler.getValue($todoID, 'isDeleted')) {
					return false;
				}

				return true;
				break;

			// edit todo
			case 'edit':
				return (this._elements[elementID].data('canEdit'));
				break;
		}

		return false;
	},

	/**
	 * @see	WCF.InlineEditor._execute()
	 */
	_execute: function(elementID, optionName) {
		// abort if option is invalid or not accessible
		if (!this._validate(elementID, optionName)) {
			return false;
		}

		switch (optionName) {
			case 'enable':
			case 'disable':
				this._updateTodo(elementID, optionName, { isDisabled: (optionName == 'enable' ? 0 : 1) });
				break;

			case 'delete':
				WCF.System.Confirmation.show(WCF.Language.get('wcf.todo.confirmDelete'), $.proxy(function(action) {
					if (action == 'confirm') {
						this._updateTodo(elementID, optionName, { deleted: 1 });
					}
				}, this));
				break;

			case 'restore':
				this._updateTodo(elementID, optionName, { isDeleted: 0 });
				break;

			case 'trash':
				WCF.System.Confirmation.show(WCF.Language.get('wcf.todo.confirmTrash'), $.proxy(function(action) {
					if (action == 'confirm') {
						this._updateTodo(elementID, optionName, { isDeleted: 1, reason: $('#wcfSystemConfirmationContent').find('textarea').val() });
					}
				}, this), { }, $('<fieldset><dl><dt>' + WCF.Language.get('wcf.todo.confirmTrash.reason') + '</dt><dd><textarea cols="40" rows="4" /></dd></dl></fieldset>'));
				break;

			case 'edit':
				window.location = this._elements[elementID].data('editUrl');
				break;

			default:
				return false;
				break;
		}

		return true;
	},

	/**
	 * Updates todo properties.
	 *
	 * @param	string		elementID
	 * @param	string		optionName
	 * @param	object		data
	 */
	_updateTodo: function(elementID, optionName, data) {
		var $todoID = this._elements[elementID].data('todoID');

		if (optionName == 'delete') {
			new WCF.Action.Proxy({
				autoSend: true,
				data: {
					actionName: 'delete',
					className: 'wcf\\data\\todo\\ToDoAction',
					objectIDs: [ $todoID ]
				},
				success: $.proxy(function(data) {
					this._updateHandler.update($todoID, data.returnValues.todoData[$todoID]);
				}, this)
			});
		}
		else {
			this._updateData.push({
				data: data,
				elementID: elementID,
				optionName: optionName
			});

			this._proxy.setOption('data', {
				actionName: optionName,
				className: 'wcf\\data\\todo\\ToDoAction',
				objectIDs: [ this._elements[elementID].data('todoID') ],
				parameters: {
					data: data
				}
			});
			this._proxy.sendRequest();
		}
	},

	/**
	 * @see	WCF.InlineEditor._updateState()
	 */
	_updateState: function() {
		this._notification.show();

		for (var $i = 0, $length = this._updateData.length; $i < $length; $i++) {
			var $data = this._updateData[$i];

			var $todoID = $('#' + $data.elementID).data('todoID');
			this._updateHandler.update($todoID, $data.data);
		}
	},

	/**
	 * Handles AJAX responses.
	 *
	 * @param	object		data
	 * @param	string		textStatus
	 * @param	jQuery		jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		this._super(data, textStatus, jqXHR);

		for (var $todoID in data.returnValues.todoData) {
			// delete note
			if (data.returnValues.todoData[$todoID].deleteNote) {
				this._updateHandler.update($todoID, data.returnValues.todoyData[$todoID]);
			}
		}
	},


	/**
	 * Sets permissions.
	 *
	 * @param	object		permissions
	 */
	setPermissions: function(permissions) {
		for (var $permission in permissions) {
			this._permissions[$permission] = permissions[$permission];
		}
	},

	/**
	 * Returns true if the active user has the given permission.
	 *
	 * @param	string		permission
	 * @return	integer
	 */
	_getPermission: function(permission) {
		if (this._permissions[permission]) {
			return this._permissions[permission];
		}

		return 0;
	},

	/**
	 * Sets current editor environment.
	 *
	 * @param	string		environment
	 */
	setEnvironment: function(environment) {
		if (environment != 'list') {
			environment = 'todo';
		}
		
		this._environment = environment;
	}
});

/**
 * Todo update handler for todo list page.
 *
 * @see	WCF.Todo.UpdateHandler
 */
WCF.Todo.UpdateHandler.List = WCF.Todo.UpdateHandler.extend({
	/**
	 * @see	WCF.Todo.UpdateHandler._enable()
	 */
	_enable: function(todoID) {
		this._super(todoID);
		this._todos[todoID].removeClass('messageDisabled');
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._disable()
	 */
	_disable: function(todoID) {
		this._super(todoID);

		this._todos[todoID].addClass('messageDisabled');
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._trash()
	 */
	_trash: function(todoID) {
		this._super(todoID);

		this._todos[todoID].addClass('messageDeleted');
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._delete()
	 */
	_delete: function(todoID, categoryLink) {
		this._todos[todoID].remove();
		delete this._todos[todoID];
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._deleteNote()
	 */
	_deleteNote: function(todoID, reason) {
		$('<p class="todoDeleteNote messageFooterNote">'+reason+'</p>').appendTo(this._todos[todoID].find('.messageFooter'));
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._restore()
	 */
	_restore: function(todoID) {
		this._super(todoID);

		this._todos[todoID].removeClass('messageDeleted');
		this._todos[todoID].find('.messageFooter > .todoDeleteNote').remove();
	}
});

/**
 * Todo update handler for todo page.
 *
 * @see		WCF.Todo.UpdateHandler
 */
WCF.Todo.UpdateHandler.Todo = WCF.Todo.UpdateHandler.extend({
	/**
	 * @see	WCF.Todo.UpdateHandler._enable()
	 */
	_enable: function(todoID, ignorePosts) {
		this._super(todoID);

		$('.sidebar').removeClass('disabled');
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._disable()
	 */
	_disable: function(todoID) {
		this._super(todoID);

		$('.sidebar').addClass('disabled');
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._trash()
	 */
	_trash: function(todoID) {
		this._super(todoID);

		$('.sidebar').addClass('deleted');
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._delete()
	 */
	_delete: function(todoID, categoryLink) {
		window.location = categoryLink;
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._deleteNote()
	 */
	_deleteNote: function(todoID, reason) {
		$('<fieldset class="todoDeleteNote"><small>'+reason+'</small></fieldset>').appendTo($('.sidebar > div'));
	},

	/**
	 * @see	WCF.Todo.UpdateHandler._restore()
	 */
	_restore: function(todoID) {
		this._super(todoID);

		$('.sidebar').removeClass('deleted');
		$('.sidebar > div > .todoDeleteNote').remove();
	}
});

/**
 * Provides extended actions for todo clipboard actions.
 */
WCF.Todo.Clipboard = Class.extend({
	/**
	 * todo update handler
	 * @var	WCF.Todo.UpdateHandler
	 */
	_updateHandler: null,

	/**
	 * Initializes a new WCF.Todo.Clipboard object.
	 *
	 * @param	WCF.Todo.UpdateHandler	updateHandler
	 */
	init: function(updateHandler) {
		this._updateHandler = updateHandler;

		// bind listener
		$('.jsClipboardEditor').each($.proxy(function(index, container) {
			var $container = $(container);
			var $types = eval($container.data('types'));

			if (WCF.inArray('de.mysterycode.wcf.toDo.toDo', $types)) {
				$container.on('clipboardAction', $.proxy(this._execute, this));
				$container.on('clipboardActionResponse', $.proxy(this._evaluateResponse, this));
				return false;
			}
		}, this));
	},

	/**
	 * Handles clipboard actions.
	 *
	 * @param	object		event
	 * @param	string		type
	 * @param	string		actionName
	 * @param	object		parameters
	 */
	_execute: function(event, type, actionName, parameters) {
		// ignore unrelated events
		if (type !== 'de.mysterycode.wcf.toDo.toDo') {
			return;
		}

		// execute action
		switch (actionName) {
			case 'de.mysterycode.wcf.toDo.toDo.trash':
				WCF.System.Confirmation.show(WCF.Language.get('wcf.todo.confirmTrash'), $.proxy(this._trash, this, parameters), { }, $('<fieldset><dl><dt>' + WCF.Language.get('wcf.todo.confirmTrash.reason') + '</dt><dd><textarea cols="40" rows="4" /></dd></dl></fieldset>'));
				break;
		}
	},

	/**
	 * Trashes todos.
	 *
	 * @param	object		parameters
	 * @param	string		action
	 */
	_trash: function(parameters, action) {
		if (action == 'confirm') {
			var $reason = $('#wcfSystemConfirmationContent').find('textarea').val();

			new WCF.Action.Proxy({
				autoSend: true,
				data: {
					actionName: 'trash',
					className: 'wcf\\data\\todo\\ToDoAction',
					objectIDs: parameters.objectIDs,
					parameters: {
						data: {
							reason: $reason
						}
					}
				},
				success: $.proxy(function(data, textStatus, jqXHR) {
					this._evaluateResponse(null, data, 'de.mysterycode.wcf.toDo.toDo', 'de.mysterycode.wcf.toDo.toDo.trash', null);
				}, this)
			});
		}
	},

	/**
	 * Evaluates AJAX responses.
	 *
	 * @param	object		event
	 * @param	object		data
	 * @param	string		type
	 * @param	string		actionName
	 * @param	object		parameters
	 */
	_evaluateResponse: function(event, data, type, actionName, parameters) {
		// ignore unrelated events
		if (type !== 'de.mysterycode.wcf.toDo.toDo') {
			return;
		}

		if (!data.returnValues.todoData || !$.getLength(data.returnValues.todoData)) {
			return;
		}

		// loop through todos
		for (var $todoID in data.returnValues.todoData) {
			this._updateHandler.update($todoID, data.returnValues.todoData[$todoID]);
		}
	}
});




