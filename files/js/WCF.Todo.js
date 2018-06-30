/**
 * Todo-related classes.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 */

/**
 * Todo namespace
 */
WCF.Todo = {};

/**
 * Loads todo previews.
 *
 * @inheritDoc
 */
WCF.Todo.Preview = WCF.Popover.extend({
	/**
	 * action proxy
	 *
	 * @var WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * list of todos
	 *
	 * @var object
	 */
	_todoProfile: {},

	/**
	 * @inheritDoc
	 */
	init: function () {
		this._super('.todoLink');

		this._proxy = new WCF.Action.Proxy({
			showLoadingOverlay: false
		});
	},

	/**
	 * @inheritDoc
	 */
	_loadContent: function () {
		var $element = $('#' + this._activeElementID);
		var $todoID = $element.data('todoID');

		if (this._todoProfile[$todoID]) {
			// use cached todolist
			this._insertContent(this._activeElementID,
				this._todoProfile[$todoID], true
			);
		} else {
			this._proxy.setOption('data', {
				actionName: 'getToDoProfile',
				className: 'wcf\\data\\todo\\ToDoAction',
				objectIDs: [$todoID]
			});

			var $elementID = this._activeElementID;
			var self = this;
			this._proxy.setOption('success', function (data, textStatus, jqXHR) {
				// cache todo
				self._todoProfile[$todoID] = data.returnValues.template;

				// show todo
				self._insertContent($elementID, data.returnValues.template,
					true
				);
			});
			this._proxy.setOption('failure', function (data, jqXHR, textStatus,
			                                           errorThrown) {
				// cache todo
				self._todoProfile[$todoID] = data.message;

				// show todo
				self._insertContent($elementID, data.message, true);

				return false;
			});
			this._proxy.sendRequest();
		}
	}
});

/**
 * Like support for todos
 *
 * @inheritDoc
 */
WCF.Todo.Like = WCF.Like.extend({
	/**
	 * @inheritDoc
	 */
	_getContainers: function () {
		return $('article.message:not(.messageCollapsed)');
	},

	/**
	 * @inheritDoc
	 */
	_getObjectID: function (containerID) {
		return this._containers[containerID].data('todoID');
	},

	/**
	 * @inheritDoc
	 */
	_getWidgetContainer: function (containerID) {
		return this._containers[containerID].find('.messageHeader');
	},

	/**
	 * @inheritDoc
	 */
	_buildWidget: function (containerID, likeButton, dislikeButton, badge,
	                        summary) {
		var $widgetContainer = this._getWidgetContainer(containerID);
		if (this._canLike) {
			var $smallButtons = this._containers[containerID]
			.find('.smallButtons');
			likeButton.insertBefore($smallButtons.find('.toTopLink'));
			dislikeButton.insertBefore($smallButtons.find('.toTopLink'));
			dislikeButton.find('a').addClass('button');
			likeButton.find('a').addClass('button');
		}

		if (summary) {
			summary.appendTo(this._containers[containerID]
			.find('.messageBody > .messageFooter'));
			summary.addClass('messageFooterNote');
		}
		$widgetContainer.find('.permalink').after(badge);
	},

	/**
	 * Sets button active state.
	 *
	 * @param jquery
	 *            likeButton
	 * @param jquery
	 *            dislikeButton
	 * @param integer
	 *            likeStatus
	 */
	_setActiveState: function (likeButton, dislikeButton, likeStatus) {
		likeButton = likeButton.find('.button').removeClass('active');
		dislikeButton = dislikeButton.find('.button').removeClass('active');

		if (likeStatus == 1) {
			likeButton.addClass('active');
		} else if (likeStatus == -1) {
			dislikeButton.addClass('active');
		}
	},

	/**
	 * @inheritDoc
	 */
	_addWidget: function (containerID, widget) {
	}
});

/**
 * Like support for todos on detail page
 *
 * @inheritDoc
 */
WCF.Todo.Like.Detail = WCF.Todo.Like.extend({
	/**
	 * @inheritDoc
	 */
	_getContainers: function () {
		return $('.boxHeadline');
	},

	/**
	 * @inheritDoc
	 */
	_buildWidget: function (containerID, likeButton, dislikeButton, badge,
	                        summary) {
		var $widgetContainer = this._getWidgetContainer(containerID);
		if (this._canLike) {
			var $smallButtons = this._containers[containerID]
			.find('.buttonGroup');
			likeButton.insertBefore($smallButtons.find('.jsReportTodo'));
			dislikeButton.insertBefore($smallButtons.find('.jsReportTodo'));
			dislikeButton.find('a').addClass('button');
			likeButton.find('a').addClass('button');
		}

		if (summary) {
			summary.insertAfter(this._containers[containerID].find('p'));
		}
		this._containers[containerID].find('h1').append(' ').append(badge);
	}
});

/**
 * Provides a generic update handler for todos.
 */
WCF.Todo.UpdateHandler = Class.extend({
	/**
	 * list of todos
	 *
	 * @var object
	 */
	_todos: {},

	/**
	 * Initializes the todo update handler.
	 */
	init: function () {
		var self = this;
		$('.jsTodo').each($.proxy(function (index, todo) {
			var $todo = $(todo);
			self._todos[$todo.data('todoID')] = $todo;
		}, this));
	},

	/**
	 * Updates a set of properties for given todo id.
	 *
	 * @param integer
	 *            todoID
	 * @param object
	 *            data
	 */
	update: function (todoID, data, reloadClipboard) {
		if (!this._todos[todoID]) {
			console.debug("[WCF.Todo.UpdateHandler] Unknown todo id " + todoID);
			return;
		}

		for (var $property in data) {
			this._updateProperty(todoID, $property, data[$property]);
		}

		if (reloadClipboard !== false) {
			WCF.Clipboard.reload();
		}
	},

	/**
	 * Wrapper for property updating.
	 *
	 * @param integer
	 *            todoID
	 * @param string
	 *            property
	 * @param mixed
	 *            value
	 */
	_updateProperty: function (todoID, property, value) {
		switch (property) {
			case 'isDisabled':
				if (value) {
					this._disable(todoID);
				} else {
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
				} else {
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
	 * Handles custom properties not known to _updateProperty(),
	 * override if necessary.
	 *
	 * @param integer
	 *            todoID
	 * @param string
	 *            property
	 * @param mixed
	 *            value
	 */
	_handleCustomProperty: function (todoID, property, value) {
		this._todos[todoID].trigger('todoUpdateHandlerProperty', [todoID, property, value]);
	},

	/**
	 * Enables a todo.
	 *
	 * @param integer
	 *            todoID
	 */
	_enable: function (todoID) {
		this._todos[todoID].data('isDisabled', 0);
	},

	/**
	 * Disables a todo.
	 *
	 * @param integer
	 *            todoID
	 */
	_disable: function (todoID) {
		this._todos[todoID].data('isDisabled', 1);
	},

	/**
	 * Trashes a todo.
	 *
	 * @param integer
	 *            todoID
	 */
	_trash: function (todoID) {
		this._todos[todoID].data('isDeleted', 1);
	},

	/**
	 * Deletes a todo.
	 *
	 * @param integer
	 *            todoID
	 * @param string
	 *            categoryLink
	 */
	_delete: function (todoID, categoryLink) {
	},

	/**
	 * Displays the delete notice.
	 *
	 * @param integer
	 *            todoID
	 * @param string
	 *            message
	 */
	_deleteNote: function (todoID, reason) {
	},

	/**
	 * Restores a todo.
	 *
	 * @param integer
	 *            todoID
	 */
	_restore: function (todoID) {
		this._todos[todoID].data('isDeleted', 0);
	},

	/**
	 * Returns generic property values for a todo.
	 *
	 * @param integer
	 *            todoID
	 * @param string
	 *            property
	 * @return boolean
	 */
	getValue: function (todoID, property) {
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
 * @param string
 *            elementSelector
 */
WCF.Todo.InlineEditor = WCF.InlineEditor.extend({
	/**
	 * current editor environment
	 *
	 * @var string
	 */
	_environment: 'todo',

	/**
	 * list of permissions
	 *
	 * @var object
	 */
	_permissions: {},

	/**
	 * todo update handler
	 *
	 * @var WCF.Todo.UpdateHandler
	 */
	_updateHandler: null,

	/**
	 * @inheritDoc
	 */
	_setOptions: function () {
		this._environment = 'todo';

		this._options = [
			// isDisabled
			{
				label: WCF.Language.get('wcf.todo.edit.enable'),
				optionName: 'enable'
			}, {
				label: WCF.Language.get('wcf.todo.edit.disable'),
				optionName: 'disable'
			},

			// isDeleted
			{
				label: WCF.Language.get('wcf.todo.edit.trash'),
				optionName: 'trash'
			}, {
				label: WCF.Language.get('wcf.todo.edit.restore'),
				optionName: 'restore'
			}, {
				label: WCF.Language.get('wcf.todo.edit.delete'),
				optionName: 'delete'
			},

			// divider
			{
				optionName: 'divider'
			},

			// edit todo
			{
				label: WCF.Language.get('wcf.todo.edit'),
				optionName: 'edit',
				isQuickOption: true
			}];
	},

	/**
	 * Returns current update handler.
	 *
	 * @return WCF.Todo.UpdateHandler
	 */
	setUpdateHandler: function (updateHandler) {
		this._updateHandler = updateHandler;
	},

	/**
	 * @inheritDoc
	 */
	_getTriggerElement: function (element) {
		return element.find('.jsTodoInlineEditor');
	},

	/**
	 * @inheritDoc
	 */
	_show: function (event) {
		var $elementID = $(event.currentTarget).data('elementID');

		// build dropdown
		var $trigger = null;
		if (!this._dropdowns[$elementID]) {
			$trigger = this._getTriggerElement(
				this._elements[$elementID]).addClass(
				'dropdownToggle');
			var $li = $trigger.parent().addClass('dropdown');
			this._dropdowns[$elementID] = $(
				'<ul class="dropdownMenu" />')
			.insertAfter($trigger);

			// force message options to stay visible while dropdown menu
			// is shown
			WCF.Dropdown.registerCallback($li.wcfIdentify(), $.proxy(
				function (containerID, action) {
					WCF.Dropdown.getDropdown(containerID).parents(
						'.messageOptions').toggleClass(
						'forceOpen');
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
	 * @inheritDoc
	 */
	_validate: function (elementID, optionName) {
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

				return this._updateHandler.getValue($todoID, 'isDisabled');

				break;

			case 'disable':
				if (!this._getPermission('canEnableTodo')) {
					return false;
				}

				if (this._updateHandler.getValue($todoID, 'isDeleted')) {
					return false;
				}

				return !this._updateHandler.getValue($todoID, 'isDisabled');

				break;

			// isDeleted
			case 'trash':
				if (!this._getPermission('canDeleteTodo')) {
					return false;
				}

				return !this._updateHandler.getValue($todoID, 'isDeleted');

				break;

			case 'delete':
				if (!this._getPermission('canDeleteTodoCompletely')) {
					return false;
				}

				return this._updateHandler.getValue($todoID, 'isDeleted');

				break;

			case 'restore':
				if (!this._getPermission('canRestoreTodo')) {
					return false;
				}

				return this._updateHandler.getValue($todoID, 'isDeleted');

				break;

			// edit todo
			case 'edit':
				return (this._getPermission('canEditTodo'));
				break;
		}

		return false;
	},

	/**
	 * @inheritDoc
	 */
	_execute: function (elementID, optionName) {
		// abort if option is invalid or not accessible
		if (!this._validate(elementID, optionName)) {
			return false;
		}

		switch (optionName) {
			case 'enable':
			case 'disable':
				this._updateTodo(elementID, optionName, {
					isDisabled: (optionName == 'enable' ? 0 : 1)
				});
				break;

			case 'delete':
				WCF.System.Confirmation.show(WCF.Language
				.get('wcf.todo.confirmDelete'), $.proxy(function (action) {
					if (action == 'confirm') {
						this._updateTodo(elementID, optionName, {
							deleted: 1
						});
					}
				}, this));
				break;

			case 'restore':
				this._updateTodo(elementID, optionName, {
					isDeleted: 0
				});
				break;

			case 'trash':
				WCF.System.Confirmation
				.show(
					WCF.Language.get('wcf.todo.confirmTrash'),
					$
					.proxy(
						function (action) {
							if (action == 'confirm') {
								this
								._updateTodo(
									elementID,
									optionName,
									{
										isDeleted: 1,
										reason: $(
											'#wcfSystemConfirmationContent')
										.find(
											'textarea')
										.val()
									}
								);
							}
						}, this),
					{},
					$('<fieldset><dl><dt>'
						+ WCF.Language
						.get('wcf.todo.confirmTrash.reason')
						+ '</dt><dd><textarea cols="40" rows="4" /></dd></dl></fieldset>')
				);
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
	 * @param string
	 *            elementID
	 * @param string
	 *            optionName
	 * @param object
	 *            data
	 */
	_updateTodo: function (elementID, optionName, data) {
		var $todoID = this._elements[elementID].data('todoID');

		if (optionName == 'delete') {
			new WCF.Action.Proxy({
				autoSend: true,
				data: {
					actionName: 'delete',
					className: 'wcf\\data\\todo\\ToDoAction',
					objectIDs: [$todoID]
				},
				success: $.proxy(function (data) {
					this._updateHandler.update(
						$todoID,
						data.returnValues.todoData[$todoID]
					);
				}, this)
			});
		} else {
			this._updateData.push({
				data: data,
				elementID: elementID,
				optionName: optionName
			});

			this._proxy.setOption(
				'data',
				{
					actionName: optionName,
					className: 'wcf\\data\\todo\\ToDoAction',
					objectIDs: [this._elements[elementID]
					.data('todoID')],
					parameters: {
						data: data
					}
				}
			);
			this._proxy.sendRequest();
		}
	},

	/**
	 * @inheritDoc
	 */
	_updateState: function () {
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
	 * @param object
	 *            data
	 * @param string
	 *            textStatus
	 * @param jQuery
	 *            jqXHR
	 */
	_success: function (data, textStatus, jqXHR) {
		this._super(data, textStatus, jqXHR);

		for (var $todoID in data.returnValues.todoData) {
			// delete note
			if (data.returnValues.todoData[$todoID].deleteNote) {
				this._updateHandler.update(
					$todoID,
					data.returnValues.todoData[$todoID]
				);
			}
		}
	},

	/**
	 * Sets permissions.
	 *
	 * @param object
	 *            permissions
	 */
	setPermissions: function (permissions) {
		for (var $permission in permissions) {
			this._permissions[$permission] = permissions[$permission];
		}
	},

	/**
	 * Returns true if the active user has the given permission.
	 *
	 * @param string
	 *            permission
	 * @return integer
	 */
	_getPermission: function (permission) {
		if (this._permissions[permission]) {
			return this._permissions[permission];
		}

		return 0;
	},

	/**
	 * Sets current editor environment.
	 *
	 * @param string
	 *            environment
	 */
	setEnvironment: function (environment) {
		if (environment != 'list') {
			environment = 'todo';
		}

		this._environment = environment;
	}
});

/**
 * Todo update handler for todo list page.
 *
 * @inheritDoc
 */
WCF.Todo.UpdateHandler.List = WCF.Todo.UpdateHandler.extend({
	/**
	 * @inheritDoc
	 */
	_enable: function (todoID) {
		this._super(todoID);
		this._todos[todoID].removeClass('messageDisabled');
	},

	/**
	 * @inheritDoc
	 */
	_disable: function (todoID) {
		this._super(todoID);

		this._todos[todoID].addClass('messageDisabled');
	},

	/**
	 * @inheritDoc
	 */
	_trash: function (todoID) {
		this._super(todoID);

		this._todos[todoID].addClass('messageDeleted');
	},

	/**
	 * @inheritDoc
	 */
	_delete: function (todoID, categoryLink) {
		this._todos[todoID].remove();
		delete this._todos[todoID];

		WCF.Clipboard.reload();
	},

	/**
	 * @inheritDoc
	 */
	_deleteNote: function (todoID, reason) {
		$('<p class="todoDeleteNote messageFooterNote">' + reason + '</p>')
		.appendTo(this._todos[todoID].find('.messageFooter'));
	},

	/**
	 * @inheritDoc
	 */
	_restore: function (todoID) {
		this._super(todoID);

		this._todos[todoID].removeClass('messageDeleted');
		this._todos[todoID].find('.messageFooter > .todoDeleteNote').remove();
	}
});

/**
 * Todo update handler for todo page.
 *
 * @inheritDoc
 */
WCF.Todo.UpdateHandler.Todo = WCF.Todo.UpdateHandler.extend({
	/**
	 * @inheritDoc
	 */
	_enable: function (todoID) {
		this._super(todoID);

		$('.todoContainer').removeClass('todoDisabled');

		$('.sidebar').removeClass('disabled');
	},

	/**
	 * @inheritDoc
	 */
	_disable: function (todoID) {
		this._super(todoID);

		$('.sidebar').addClass('disabled');
	},

	/**
	 * @inheritDoc
	 */
	_trash: function (todoID) {
		this._super(todoID);

		$('.sidebar').addClass('deleted');
	},

	/**
	 * @inheritDoc
	 */
	_delete: function (todoID, categoryLink) {
		window.location = categoryLink;
	},

	/**
	 * @inheritDoc
	 */
	_deleteNote: function (todoID, reason) {
		$(
			'<fieldset class="todoDeleteNote"><small>' + reason
			+ '</small></fieldset>').appendTo($('.sidebar > div'));
	},

	/**
	 * @inheritDoc
	 */
	_restore: function (todoID) {
		this._super(todoID);

		$('.todoContainer').removeClass('todoDeleted');

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
	 *
	 * @var WCF.Todo.UpdateHandler
	 */
	_updateHandler: null,

	/**
	 * Initializes a new WCF.Todo.Clipboard object.
	 *
	 * @param WCF.Todo.UpdateHandler
	 *            updateHandler
	 */
	init: function (updateHandler) {
		this._updateHandler = updateHandler;

		// bind listener
		$('.jsClipboardEditor').each(
			$.proxy(function (index, container) {
				var $container = $(container);
				var $types = eval($container.data('types'));

				if (WCF.inArray(
						'de.mysterycode.wcf.toDo.toDo',
						$types
					)) {
					$container.on('clipboardAction', $.proxy(
						this._execute, this));
					$container.on('clipboardActionResponse', $
					.proxy(this._evaluateResponse, this));
					return false;
				}
			}, this));
	},

	/**
	 * Handles clipboard actions.
	 *
	 * @param object
	 *            event
	 * @param string
	 *            type
	 * @param string
	 *            actionName
	 * @param object
	 *            parameters
	 */
	_execute: function (event, type, actionName, parameters) {
		// ignore unrelated events
		if (type !== 'de.mysterycode.wcf.toDo.toDo') {
			return;
		}

		// execute action
		switch (actionName) {
			case 'de.mysterycode.wcf.toDo.toDo.trash':
				WCF.System.Confirmation
				.show(
					WCF.Language.get('wcf.todo.confirmTrash'),
					$.proxy(this._trash, this, parameters),
					{},
					$('<fieldset><dl><dt>'
						+ WCF.Language
						.get('wcf.todo.confirmTrash.reason')
						+ '</dt><dd><textarea cols="40" rows="4" /></dd></dl></fieldset>')
				);
				break;
		}
	},

	/**
	 * Trashes todos.
	 *
	 * @param object
	 *            parameters
	 * @param string
	 *            action
	 */
	_trash: function (parameters, action) {
		if (action == 'confirm') {
			var $reason = $('#wcfSystemConfirmationContent').find(
				'textarea').val();

			new WCF.Action.Proxy(
				{
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
					success: $
					.proxy(
						function (data, textStatus,
						          jqXHR) {
							this
							._evaluateResponse(
								null,
								data,
								'de.mysterycode.wcf.toDo.toDo',
								'de.mysterycode.wcf.toDo.toDo.trash',
								null
							);
						}, this)
				});
		}
	},

	/**
	 * Evaluates AJAX responses.
	 *
	 * @param object
	 *            event
	 * @param object
	 *            data
	 * @param string
	 *            type
	 * @param string
	 *            actionName
	 * @param object
	 *            parameters
	 */
	_evaluateResponse: function (event, data, type, actionName,
	                             parameters) {
		// ignore unrelated events
		if (type !== 'de.mysterycode.wcf.toDo.toDo') {
			return;
		}

		if (!data.returnValues.todoData
			|| !$.getLength(data.returnValues.todoData)) {
			return;
		}

		// loop through todos
		for (var $todoID in data.returnValues.todoData) {
			this._updateHandler.update(
				$todoID,
				data.returnValues.todoData[$todoID]
			);
		}
	}
});

/**
 * Adds the current user as responsible
 */
WCF.Todo.Participate = Class.extend({
	/**
	 * list of buttons
	 *
	 * @var object
	 */
	_buttons: {},

	/**
	 * button selector
	 *
	 * @var string
	 */
	_buttonSelector: '',

	/**
	 * dialog overlay
	 *
	 * @var jQuery
	 */
	_dialog: null,

	/**
	 * notification object
	 *
	 * @var WCF.System.Notification
	 */
	_notification: null,

	/**
	 * object id
	 *
	 * @var integer
	 */
	_objectID: 0,

	/**
	 * user id
	 *
	 * @var integer
	 */
	_userID: 0,

	/**
	 * action proxy
	 *
	 * @var WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * Creates a new WCF.Todo.Participate object.
	 *
	 * @param string
	 *            objectType
	 * @param string
	 *            buttonSelector
	 */
	init: function (buttonSelector) {
		this._buttonSelector = buttonSelector;

		this._buttons = {};
		this._notification = null;
		this._objectID = 0;
		this._userID = 0;
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._initButtons();

		WCF.DOMNodeInsertedHandler.addCallback(
			'WCF.Todo.Participate',
			$.proxy(this._initButtons, this)
		);
	},

	/**
	 * Initializes the feature for all matching buttons.
	 */
	_initButtons: function () {
		var self = this;
		$(this._buttonSelector).each(function (index, button) {
			var $button = $(button);
			var $buttonID = $button.wcfIdentify();

			if (!self._buttons[$buttonID]) {
				self._buttons[$buttonID] = $button;
				$button.click($.proxy(self._click, self));
			}
		});
	},

	/**
	 * Handles clicks on the button.
	 *
	 * @param object
	 *            event
	 */
	_click: function (event) {
		this._objectID = $(event.currentTarget).data('objectID');
		this._userID = $(event.currentTarget).data('userID');

		this._showDialog();
		this._dialog.find('.jsSubmitParticipate').click(
			$.proxy(this._submit, this));
	},

	/**
	 * Handles successful AJAX requests.
	 *
	 * @param object
	 *            data
	 * @param string
	 *            textStatus
	 * @param jQuery
	 *            jqXHR
	 */
	_success: function (data, textStatus, jqXHR) {
		if (data.returnValues.submitted) {
			if (this._notification === null) {
				this._notification = new WCF.System.Notification(
					WCF.Language
					.get('wcf.toDo.task.participate.success'));
			}

			this._dialog.wcfDialog('close');
			this._notification.show();

			setTimeout(function () {
				location.reload();
			}, 1000);
		}
	},

	/**
	 * Displays the dialog overlay.
	 *
	 * @param string
	 *            template
	 */
	_showDialog: function () {
		if (this._dialog === null) {
			this._dialog = $('#participateDialog');
			if (!this._dialog.length) {
				this._dialog = $('<div id="participateDialog" />')
				.hide().appendTo(document.body);
			}
		}

		var html = WCF.Language.get('wcf.toDo.task.participate.shure')
			+ '<div class="formSubmit">'
			+ '<button class="jsSubmitParticipate buttonPrimary" accesskey="s" autofocus>'
			+ WCF.Language.get('wcf.global.button.submit')
			+ '</button>' + '</div>';

		this._dialog.html(html).wcfDialog({
			title: WCF.Language.get('wcf.toDo.task.participate')
		}).wcfDialog('render');
	},

	/**
	 * Submits the request.
	 */
	_submit: function () {
		this._proxy.setOption('data', {
			actionName: 'participate',
			className: 'wcf\\data\\todo\\ToDoAction',
			parameters: {
				userID: this._userID,
				objectID: this._objectID
			}
		});
		this._proxy.sendRequest();
	}
});

/**
 * Changes the status into solved
 */
WCF.Todo.MarkSolved = Class.extend({
	/**
	 * list of buttons
	 *
	 * @var object
	 */
	_buttons: {},

	/**
	 * button selector
	 *
	 * @var string
	 */
	_buttonSelector: '',

	/**
	 * dialog overlay
	 *
	 * @var jQuery
	 */
	_dialog: null,

	/**
	 * notification object
	 *
	 * @var WCF.System.Notification
	 */
	_notification: null,

	/**
	 * object id
	 *
	 * @var integer
	 */
	_objectID: 0,

	/**
	 * user id
	 *
	 * @var integer
	 */
	_userID: 0,

	/**
	 * action proxy
	 *
	 * @var WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * Creates a new WCF.Todo.MarkSolved object.
	 *
	 * @param string
	 *            objectType
	 * @param string
	 *            buttonSelector
	 */
	init: function (buttonSelector) {
		this._buttonSelector = buttonSelector;

		this._buttons = {};
		this._notification = null;
		this._objectID = 0;
		this._userID = 0;
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._initButtons();

		WCF.DOMNodeInsertedHandler.addCallback('WCF.Todo.MarkSolved', $
		.proxy(this._initButtons, this));
	},

	/**
	 * Initializes the feature for all matching buttons.
	 */
	_initButtons: function () {
		var self = this;
		$(this._buttonSelector).each(function (index, button) {
			var $button = $(button);
			var $buttonID = $button.wcfIdentify();

			if (!self._buttons[$buttonID]) {
				self._buttons[$buttonID] = $button;
				$button.click($.proxy(self._click, self));
			}
		});
	},

	/**
	 * Handles clicks on the button.
	 *
	 * @param object
	 *            event
	 */
	_click: function (event) {
		this._objectID = $(event.currentTarget).data('objectID');
		this._userID = $(event.currentTarget).data('userID');

		this._showDialog();
		this._dialog.find('.jsSubmitMarkSolved').click(
			$.proxy(this._submit, this));
	},

	/**
	 * Handles successful AJAX requests.
	 *
	 * @param object
	 *            data
	 * @param string
	 *            textStatus
	 * @param jQuery
	 *            jqXHR
	 */
	_success: function (data, textStatus, jqXHR) {
		if (data.returnValues.success) {
			if (this._notification === null) {
				this._notification = new WCF.System.Notification(
					WCF.Language.get('wcf.toDo.task.solve.success'));
			}

			this._dialog.wcfDialog('close');
			this._notification.show();

			setTimeout(function () {
				location.reload();
			}, 1000);
		}
	},

	/**
	 * Displays the dialog overlay.
	 *
	 * @param string
	 *            template
	 */
	_showDialog: function () {
		if (this._dialog === null) {
			this._dialog = $('#markSolvedDialog');
			if (!this._dialog.length) {
				this._dialog = $('<div id="markSolvedDialog" />')
				.hide().appendTo(document.body);
			}
		}

		var html = WCF.Language.get('wcf.toDo.task.solve.shure')
			+ '<div class="formSubmit">'
			+ '<button class="jsSubmitMarkSolved buttonPrimary" accesskey="s" autofocus>'
			+ WCF.Language.get('wcf.global.button.submit')
			+ '</button>' + '</div>';

		this._dialog.html(html).wcfDialog({
			title: WCF.Language.get('wcf.toDo.task.solve')
		}).wcfDialog('render');
	},

	/**
	 * Submits the request.
	 */
	_submit: function () {
		this._proxy.setOption('data', {
			actionName: 'editStatus',
			className: 'wcf\\data\\todo\\ToDoAction',
			parameters: {
				userID: this._userID,
				objectID: this._objectID,
				status: 1
			}
		});
		this._proxy.sendRequest();
	}
});

WCF.Todo.QuoteHandler = WCF.Message.Quote.Handler.extend({
	/**
	 * @inheritDoc
	 */
	init: function (quoteManager) {
		this._super(quoteManager, 'wcf\\data\\todo\\ToDoAction',
			'de.mysterycode.wcf.toDo', '.todoQuoteContainer',
			'.todoDescription',
			'.todoDescription > fieldset > div', false
		);
	}
});

WCF.Todo.UpdateProgress = Class.extend({
	/**
	 * callback object
	 *
	 * @var object
	 */
	_callback: null,

	/**
	 * dialog overlay
	 *
	 * @var jQuery
	 */
	_dialog: null,

	/**
	 * action proxy
	 *
	 * @var WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * action class name
	 *
	 * @var string
	 */
	_className: 'wcf\\data\\todo\\ToDoAction',

	/**
	 * prefix of the language items
	 *
	 * @var string
	 */
	_languageItemPrefix: 'wcf.toDo',

	_didInit: false,

	_objectID: 0,

	/**
	 * Initializes WCF.Todo.UpdateProgress on first use.
	 */
	init: function (objectID) {
		this._dialog = $('<div />').hide().appendTo(document.body);
		this._proxy = new WCF.Action.Proxy();
		this._objectID = objectID;

		if (!this._didInit) {
			this._init();
		}

		this._didInit = true;
	},

	_init: function () {
		$('.updateProgress').click($.proxy(this.prepare, this));
	},

	/**
	 * Prepares
	 */
	prepare: function (event) {
		// prepare request
		this._proxy.setOption('data', {
			actionName: 'prepareProgressUpdate',
			className: this._className,
			objectIDs: [this._objectID]
		});
		this._proxy.setOption('success', $.proxy(this._success, this));

		// send request
		this._proxy.sendRequest();
	},

	/**
	 * Handles the successful preparation of the editing.
	 *
	 * @param object
	 *            data
	 * @param string
	 *            textStatus
	 * @param jQuery
	 *            jqXHR
	 */
	_success: function (data, textStatus, jqXHR) {
		this._dialog.data('objectID', data.objectIDs[0]).html(
			data.returnValues.template);
		this._dialog.wcfDialog({
			title: WCF.Language.get('wcf.toDo.task.progress.update')
		});

		// listen for submit event
		this._dialog.find('.formSubmit > input[type=submit]').click(
			$.proxy(this._submit, this));
	},

	/**
	 * Submit new progress status
	 *
	 * @param object
	 *            event
	 */
	_submit: function (event) {
		var $parameters = this._getParameters();
		if (Object.keys($parameters).length) {
			// prepare request
			this._proxy.setOption('data', {
				actionName: 'progressUpdate',
				className: this._className,
				objectIDs: [this._objectID],
				parameters: $parameters
			});
			this._proxy.setOption('success', $.proxy(function (data) {
				this._callback(data);
			}, this));

			// send request
			this._proxy.sendRequest();

			// close dialog
			this._dialog.wcfDialog('close');
		}
	},

	_callback: function (data) {
		$('.progressbar_inner').width(
			'calc(100% - ' + data.returnValues.progress + '% + 2px)');
		$('.progressbar_text').html(
			data.returnValues.progress + ' '
			+ WCF.Language.get('wcf.toDo.task.progress.percent'))
	},

	/**
	 * Validates the form and returns the parameters to save.
	 *
	 * @return object
	 */
	_getParameters: function () {
		return {
			progress: $('#progress').val()
		};
	},

	/**
	 * Shows an inline error.
	 *
	 * @param string
	 *            selector
	 * @param string
	 *            errorField
	 * @param string
	 *            errorType
	 */
	_showInlineError: function (selector, errorField, errorType) {
		var languageVariable = this._languageItemPrefix + '.' + errorField
			+ '.error.' + errorType;
		if (errorType == 'empty') {
			languageVariable = 'wcf.global.form.error.empty';
		}

		$(selector).parent().append(
			$('<small class="innerError">'
				+ WCF.Language.get(languageVariable) + '</small>'));
	}
});

WCF.Todo.Search = {};

/**
 * Provides quick search for user groups only.
 *
 * @inheritDoc
 */
WCF.Todo.Search.User = WCF.Search.Base.extend({
	/**
	 * @inheritDoc
	 */
	_className: 'wcf\\data\\user\\group\\UserGroupSearchAction',

	/**
	 * @inheritDoc
	 */
	init: function (searchInput, callback, excludedSearchValues,
	                commaSeperated) {
		this._super(searchInput, callback, excludedSearchValues,
			commaSeperated
		);
	},

	/**
	 * @inheritDoc
	 */
	_createListItem: function (item) {
		var $listItem = this._super(item);

		var $icon = null;
		if (item.icon) {
			$icon = $(item.icon);
		} else if (item.type === 'group') {
			$icon = $('<span class="icon icon16 icon-group" />');
		}

		if ($icon) {
			var $label = $listItem.find('span').detach();

			var $box16 = $('<div />').addClass('box16').appendTo(
				$listItem);

			$box16.append($icon);
			$box16.append($('<div />').append($label));
		}

		// insert item type
		$listItem.data('type', item.type);

		return $listItem;
	}
});
