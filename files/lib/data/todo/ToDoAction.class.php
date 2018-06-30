<?php

namespace wcf\data\todo;
use wcf\data\todo\assigned\group\AssignedGroupAction;
use wcf\data\todo\assigned\user\AssignedUserAction;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IClipboardAction;
use wcf\data\IMessageInlineEditorAction;
use wcf\data\IMessageQuoteAction;
use wcf\system\attachment\AttachmentHandler;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\cache\builder\AssignCacheBuilder;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\comment\CommentHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\label\LabelHandler;
use wcf\system\like\LikeHandler;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\moderation\queue\ModerationQueueActivationManager;
use wcf\system\request\LinkHandler;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\user\activity\point\UserActivityPointHandler;
use wcf\system\user\notification\object\ToDoUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\MessageUtil;
use wcf\util\StringUtil;

/**
 * Executes todo-related actions.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoAction extends AbstractDatabaseObjectAction implements IClipboardAction, IMessageInlineEditorAction, IMessageQuoteAction {
	/**
	 * @inheritDoc
	 */
	protected $allowGuestAccess = ['getPreview', 'saveFullQuote', 'saveQuote'];
	
	/**
	 * @inheitDoc
	 */
	protected $resetCache = ['create', 'delete', 'toggle', 'update', 'updatePosition', 'enable', 'disable', 'trash', 'restore'];
	
	/**
	 * list of todo data
	 *
	 * @var array<array>
	 */
	public $todoData = [];

	/**
	 * @var ToDo
	 */
	protected $todo = null;
	
	/**
	 *
	 * @inheritDoc
	 */
	public function create() {
		if (!isset($this->parameters['data']['enableHtml'])) {
			$this->parameters['data']['enableHtml'] = 1;
		}
		
		if (!empty($this->parameters['htmlInputProcessor'])) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->parameters['data']['description'] = $this->parameters['htmlInputProcessor']->getHtml();
		}

		if (!empty($this->parameters['notesNtmlInputProcessor'])) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->parameters['data']['note'] = $this->parameters['notesHtmlInputProcessor']->getHtml();
		}

		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$data['attachments'] = count($this->parameters['attachmentHandler']);
		}

		if (LOG_IP_ADDRESS) {
			// add ip address
			if (!isset($this->parameters['data']['ipAddress'])) {
				$this->parameters['data']['ipAddress'] = WCF::getSession()->ipAddress;
			}
		} else {
			// do not track ip address
			if (isset($this->parameters['data']['ipAddress'])) {
				unset($this->parameters['data']['ipAddress']);
			}
		}

		/** @var ToDo $todo */
		$todo = parent::create();
		
		// update attachments
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['attachmentHandler']->updateObjectID($todo->todoID);
		}

		// save embedded objects
		if (!empty($this->parameters['htmlInputProcessor'])) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->parameters['htmlInputProcessor']->setObjectID($todo->todoID);
			if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['htmlInputProcessor'])) {
				$objectEditor = new ToDoEditor($todo);
				$objectEditor->update(['hasEmbeddedObjects' => 1]);
			}
		}
		if (!empty($this->parameters['notesHtmlInputProcessor'])) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->parameters['notesHtmlInputProcessor']->setObjectID($todo->todoID);
			if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notesHtmlInputProcessor'])) {
				$objectEditor = new ToDoEditor($todo);
				$objectEditor->update(['notesHasEmbeddedObjects' => 1]);
			}
		}
		
		if (!$todo->isDisabled) {
			$todoAction = new self([$todo], 'publish');
			$todoAction->executeAction();
		} else {
			ModerationQueueActivationManager::getInstance()->addModeratedContent('de.mysterycode.wcf.toDo.toDo', $todo->todoID);
		}
		
		// clear quotes
		if (isset($this->parameters['removeQuoteIDs']) && !empty($this->parameters['removeQuoteIDs'])) {
			MessageQuoteManager::getInstance()->markQuotesForRemoval($this->parameters['removeQuoteIDs']);
		}
		MessageQuoteManager::getInstance()->removeMarkedQuotes();
		
		return $todo;
	}
	
	/**
	 * Publishes the given todos for the first time.
	 */
	public function publish() {
		$this->loadTodos();

		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			if ($todo->submitter) {
				UserActivityEventHandler::getInstance()->fireEvent('de.mysterycode.wcf.toDo.toDo.recentActivityEvent', $todo->todoID, WCF::getLanguage()->languageID, $todo->submitter, $todo->time);
				UserActivityPointHandler::getInstance()->fireEvent('de.mysterycode.wcf.toDo.toDo.activityPointEvent', $todo->todoID, $todo->submitter);
				ToDoEditor::updateUserToDoCounter([$todo->submitter => 1]);
			}
			$users = [];
			$sql = "SELECT userID
				FROM wcf" . WCF_N . "_user";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([]);
			while ($row = $statement->fetchArray()) {
				$users[] = $row['userID'];
			}
			UserNotificationHandler::getInstance()->fireEvent('create', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todo->todoID)), $users);
		}
	}
	
	/**
	 *
	 * @inheritDoc
	 */
	public function update() {
		if (!isset($this->parameters['data']['enableHtml'])) {
			$this->parameters['data']['enableHtml'] = 1;
		}
		
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['data']['attachments'] = count($this->parameters['attachmentHandler']);
		}

		if (!empty($this->parameters['htmlInputProcessor'])) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->parameters['data']['description'] = $this->parameters['htmlInputProcessor']->getHtml();
		}
		if (!empty($this->parameters['notesHtmlInputProcessor'])) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->parameters['data']['note'] = $this->parameters['notesHtmlInputProcessor']->getHtml();
		}
		
		if (isset($this->parameters['data'])) {
			$todoIDs = [];
			/** @var ToDoEditor|ToDo $todo */
			foreach ($this->objects as $todo) {
				$todoIDs[] = $todo->todoID;
				if (WCF::getUser()->userID != $todo->submitter) {
					$users = array_diff(array_unique($todo->getResponsibleIDs()), [WCF::getUser()->userID]);
				} else {
					$users = array_diff(array_unique(array_merge([$todo->submitter], $todo->getResponsibleIDs())), [WCF::getUser()->userID]);
				}
				if (!empty($users)) {
					if (isset($this->parameters['data']['status']) && !isset($this->parameters['data']['title'])) {
						UserNotificationHandler::getInstance()->fireEvent('editStatus', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todo->todoID)), $users);
					} else {
						UserNotificationHandler::getInstance()->fireEvent('edit', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todo->todoID)), $users);
					}
				}
			}
		}

		// update embedded objects
		if (!empty($this->parameters['htmlInputProcessor'])) {
			/** @var ToDoEditor|ToDo $object */
			foreach ($this->getObjects() as $object) {
				$this->parameters['htmlInputProcessor']->setObjectID($object->todoID);
				if ($object->hasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['htmlInputProcessor'])) {
					$object->update(['hasEmbeddedObjects' => $object->hasEmbeddedObjects ? 0 : 1]);
				}
			}
		}
		if (!empty($this->parameters['notesHtmlInputProcessor'])) {
			/** @var ToDoEditor|ToDo $object */
			foreach ($this->getObjects() as $object) {
				$this->parameters['notesHtmlInputProcessor']->setObjectID($object->todoID);
				if ($object->notesHasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notesHtmlInputProcessor'])) {
					$object->update(['notesHasEmbeddedObjects' => $object->notesHasEmbeddedObjects ? 0 : 1]);
				}
			}
		}
		
		parent::update();
	}
	
	/**
	 * Validating parameters for enabling todos.
	 */
	public function validateEnable() {
		$this->loadTodos();

		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			if (!$todo->isDisabled || $todo->isDeleted) {
				throw new UserInputException('objectIDs');
			}
			
			if (!$todo->canEnable()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Enables given todos.
	 *
	 * @return array <array>
	 * @throws \wcf\system\exception\SystemException
	 */
	public function enable() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		// update todos
		$todoIDs = [];
		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			$todo->update([
				'isDisabled' => 0
			]);
			
			$todoIDs[] = $todo->todoID;
			$this->addToDoData($todo->getDecoratedObject(), 'isDisabled', 0);
		}
		
		$todoAction = new self($this->objects, 'publish');
		$todoAction->executeAction();
		
		$this->removeModeratedContent($todoIDs);
		
		$this->unmarkToDos();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for disabling todos.
	 */
	public function validateDisable() {
		$this->loadTodos();

		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			if ($todo->isDisabled || $todo->isDeleted) {
				throw new UserInputException( 'objectIDs' );
			}
			
			if (!$todo->canEnable()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Disables given todos.
	 *
	 * @return array <array>
	 * @throws \wcf\system\exception\SystemException
	 */
	public function disable() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		$todoIDs = [];
		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			$todo->update([
				'isDisabled' => 1
			]);
			
			$todoIDs[] = $todo->todoID;
			$this->addToDoData($todo->getDecoratedObject(), 'isDisabled', 1);
			
			ModerationQueueActivationManager::getInstance()->addModeratedContent('de.mysterycode.wcf.toDo.toDo', $todo->todoID);
		}
		
		$this->removeActivityEvents($todoIDs);
		
		$this->unmarkToDos();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for trashing todos.
	 */
	public function validateTrash() {
		$this->loadTodos();

		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			if ($todo->isDeleted) {
				throw new UserInputException('objectIDs');
			}
			
			if (!$todo->canDelete()) {
				throw new PermissionDeniedException();
			}
		}
	}
	
	/**
	 * Trashes given todos.
	 *
	 * @return array<array>
	 */
	public function trash() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		$deleteReason = (isset($this->parameters['data']['reason']) ? StringUtil::trim($this->parameters['data']['reason']) : '');
		
		$todoIDs = [];
		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			$todo->update([
				'isDeleted' => 1,
				'deleteTime' => TIME_NOW,
				'deletedByID' => WCF::getUser()->userID,
				'deletedBy' => WCF::getUser()->username,
				'deleteReason' => $deleteReason
			]);
			
			$todoIDs[] = $todo->todoID;
			$this->addToDoData($todo->getDecoratedObject(), 'isDeleted', 1);
			$this->addToDoData($todo->getDecoratedObject(), 'deleteNote', WCF::getLanguage()->getDynamicVariable('wcf.todo.deleteNote', [
				'todo' => new ToDo($todo->todoID)
			]));
		}
		
		$this->unmarkToDos();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for deleting todos.
	 */
	public function validateDelete() {
		$this->loadTodos();

		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			if (!$todo->isDeleted) {
				throw new UserInputException('objectIDs');
			}
			
			if (!$todo->canDeleteCompletely()) {
				throw new PermissionDeniedException();
			}
		}
	}

	/**
	 * Deletes given todos.
	 *
	 * @return array <array>
	 * @throws \wcf\system\exception\SystemException
	 */
	public function delete() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		$todoIDs = $userCounters = [];
		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			$todoIDs[] = $todo->todoID;
			
			if (! isset($userCounters[$todo->submitter])) {
				$userCounters[$todo->submitter] = 0;
			}
			$userCounters[$todo->submitter] --;
		}
		
		$this->removeActivityEvents($todoIDs);
		
		ToDoEditor::updateUserToDoCounter($userCounters);
		
		UserActivityPointHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.activityPointEvent', $todoIDs);
		
		CommentHandler::getInstance()->deleteObjects('de.mysterycode.wcf.toDo.toDo', $todoIDs);
		
		AttachmentHandler::removeAttachments('de.mysterycode.wcf.toDo.toDo', $todoIDs);
		
		MessageEmbeddedObjectManager::getInstance()->removeObjects('de.mysterycode.wcf.toDo', $todoIDs);
		
		// delete likes
		LikeHandler::getInstance()->removeLikes('de.mysterycode.wcf.toDo.toDo.like', $todoIDs);
		
		// delete label assignments
		LabelHandler::getInstance()->removeLabels(LabelHandler::getInstance()->getObjectType('de.mysterycode.wcf.toDo.toDo')->objectTypeID, $todoIDs);
		
		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			$todo->delete();
			$this->addToDoData($todo->getDecoratedObject(), 'deleted', LinkHandler::getInstance()->getLink('TodoList', []));
		}
		
		$this->unmarkToDos();
		
		return $this->getToDoData();
	}
	
	/**
	 * Validating parameters for restoring todos.
	 */
	public function validateRestore() {
		$this->loadTodos();

		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			if (!$todo->isDeleted) {
				throw new UserInputException('objectIDs');
			}
			
			if (!$todo->canRestore()) {
				throw new PermissionDeniedException();
			}
		}
	}
	
	/**
	 * Restores given todos.
	 *
	 * @return array<array>
	 */
	public function restore() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		$todoIDs = [];
		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			$todo->update([
				'isDeleted' => 0,
				'deleteTime' => 0,
				'deletedByID' => null,
				'deletedBy' => '',
				'deleteReason' => 0
			]);
			
			$todoIDs[] = $todo->todoID;
			$this->addToDoData($todo->getDecoratedObject(), 'isDeleted', 0);
		}
		
		$this->unmarkToDos();
		
		return $this->getToDoData();
	}
	
	/**
	 * Does nothing.
	 */
	public function validateUnmarkAll() { }
	
	/**
	 * Unmarks all todos.
	 */
	public function unmarkAll() {
		ClipboardHandler::getInstance()->removeItems(ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo'));
	}
	
	/**
	 * Loads todos for given object ids.
	 */
	protected function loadTodos() {
		if (empty($this->objectIDs)) {
			throw new UserInputException('objectIDs');
		}
		
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		if (empty($this->objects)) {
			throw new UserInputException('objectIDs');
		}
	}
	
	/**
	 * Adds the given todo data.
	 *
	 * @param \wcf\data\todo\ToDo $todo        	
	 * @param string $key        	
	 * @param mixed $value        	
	 */
	protected function addToDoData(ToDo $todo, $key, $value) {
		if (!isset($this->todoData[$todo->todoID])) {
			$this->todoData[$todo->todoID] = [];
		}
		
		$this->todoData[$todo->todoID][$key] = $value;
	}
	
	/**
	 * Returns todo data.
	 *
	 * @return array<array>
	 */
	protected function getToDoData() {
		return [
			'todoData' => $this->todoData
		];
	}

	/**
	 * Removes moderated content todos for the todos with the given todo ids.
	 *
	 * @param array $todoIDs <integer> $todoIDs
	 * @throws \wcf\system\exception\SystemException
	 */
	protected function removeModeratedContent(array $todoIDs) {
		ModerationQueueActivationManager::getInstance()->removeModeratedContent('de.mysterycode.wcf.toDo.toDo', $todoIDs);
	}

	/**
	 * Removes user activity events for the todos with the given todo ids.
	 *
	 * @param array $todoIDs <integer> $todoIDs
	 * @throws \wcf\system\exception\SystemException
	 */
	protected function removeActivityEvents(array $todoIDs) {
		UserActivityEventHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.recentActivityEvent', $todoIDs);
		UserActivityEventHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.like.recentActivityEvent', $todoIDs);
		UserActivityPointHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.activityPointEvent', $todoIDs);
	}

	/**
	 * Unmarks the todos with the given todo ids.
	 *
	 * @param array $todoIDs <integer> $todoIDs
	 * @throws \wcf\system\exception\SystemException
	 */
	protected function unmarkToDos(array $todoIDs = []) {
		if (empty($todoIDs)) {
			/** @var ToDoEditor|ToDo $todo */
			foreach ($this->objects as $todo) {
				$todoIDs[] = $todo->todoID;
			}
		}
		
		if (!empty($todoIDs)) {
			ClipboardHandler::getInstance()->unmark($todoIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo'));
		}
	}
	
	/**
	 * Handles the responsible users of a todo
	 */
	public function updateResponsibles() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		$skipDelete = false;
		
		if (!(isset($this->parameters['search']) && $this->parameters['search'] !== null))
			return;
		
		if (isset($this->parameters['skipDelete']) && $this->parameters['skipDelete'] !== false)
			$skipDelete = true;

		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			if ($todo === null)
				continue;
			
			$existingResponsibles = $todo->getResponsibleIDs() ?: [];
			
			$responsibleList = UserProfile::getUserProfilesByUsername(ArrayUtil::trim(explode(',', $this->parameters['search'])));
			$responsibleList = array_unique($responsibleList);
			
			$userIDs = $checkArray = [];
			foreach ($responsibleList as $user) {
				$checkArray[] = $user->userID;
				
				if (!in_array($user->userID, $existingResponsibles)) {
					$userIDs[] = $user->userID;
					
					$assignAction = new AssignedUserAction([], 'create', ['data' => ['todoID' => $todo->todoID, 'userID' => $user->userID, 'username' => $user->username]]);
					$assignAction->executeAction();
				}
			}
			
			if (!$skipDelete) {
				foreach ($existingResponsibles as $responsible) {
					if (!in_array($responsible, $checkArray)) {
						$assignAction = new AssignedUserAction([], 'deleteByTodo', ['userID' => $responsible, 'todoIDs' => [$todo->todoID]]);
						$assignAction->executeAction();
					}
				}
			}
			
			if (!empty($userIDs))
				UserNotificationHandler::getInstance()->fireEvent('assign', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todo->todoID)), $userIDs);
		}
		
		AssignCacheBuilder::getInstance()->reset();
	}
	
	/**
	 * Handles the responsible groups of a todo
	 */
	public function updateResponsibleGroups() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		$skipDelete = false;
		
		if (!(isset($this->parameters['search']) && $this->parameters['search'] !== null))
			return;
		
		if (isset($this->parameters['skipDelete']) && $this->parameters['skipDelete'] !== false)
			$skipDelete = true;
		
		$search = ArrayUtil::trim(explode(',', $this->parameters['search']));

		/** @var ToDoEditor|ToDo $todo */
		foreach ($this->objects as $todo) {
			if ($todo === null)
				continue;
			
			$existingResponsibleGroups = $todo->getResponsibleGroupIDs() ?: [];
			
			$accessibleGroups = UserGroup::getAccessibleGroups();
			$responsibleGroupList = $checkArray = [];
			foreach ($search as $searchItem) {
				foreach ($accessibleGroups as $group) {
					$groupName = $group->getName();
					if ($groupName == $searchItem) {
						$checkArray[] = $group->groupID;
						
						if (!in_array($group->groupID, $existingResponsibleGroups)) {
							$responsibleGroupList[] = $group->groupID;
							
							$assignAction = new AssignedGroupAction([], 'create', ['data' => ['todoID' => $todo->todoID, 'groupID' => $group->groupID, 'groupname' => $group->getName()]]);
							$assignAction->executeAction();
						}
					}
				}
			}
			
			if (!$skipDelete) {
				foreach ($existingResponsibleGroups as $responsible) {
					if (!in_array($responsible, $checkArray)) {
						$assignAction = new AssignedGroupAction([], 'deleteByTodo', ['groupID' => $responsible, 'todoIDs' => [$todo->todoID]]);
						$assignAction->executeAction();
					}
				}
			}
			
			if (!empty($responsibleGroupList)) {
				$conditions = new PreparedStatementConditionBuilder();
				$conditions->add("groupID IN (?)", [$responsibleGroupList]);
				$sql = "SELECT	userID
					FROM	wcf".WCF_N."_user_to_group
					".$conditions;
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditions->getParameters());
				
				$assignedUsers = $todo->getResponsibleIDs();
				$userIDs = [];
				while ($row = $statement->fetchArray()) {
					if (empty($assignedUsers) || !in_array($row['userID'], $assignedUsers))
						$userIDs[] = $row['userID'];
				}
			}
			
			if (!empty($userIDs))
				UserNotificationHandler::getInstance()->fireEvent('assign', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todo->todoID)), $userIDs);
		}
		
		AssignCacheBuilder::getInstance()->reset();
	}
	
	/**
	 * Validates todo profile preview.
	 */
	public function validateGetTodoProfile() {
		if (count($this->objectIDs) != 1) {
			throw new UserInputException('objectIDs');
		}
	}
	
	/**
	 * Returns todo profile preview.
	 *
	 * @return	array
	 */
	public function getTodoProfile() {
		$todoID = reset($this->objectIDs);
		
		if ($todoID) {
			$todoList = new ToDoList();
			$todoList->getConditionBuilder()->add("todo_table.id = ?", [$todoID]);
			$todoList->readObjects();
			$todoProfiles = $todoList->getObjects();
			
			if (empty($todoProfiles))
				WCF::getTPL()->assign('unknownTodo', true);
			else
				WCF::getTPL()->assign('todo', reset($todoProfiles));
		}
		else {
			WCF::getTPL()->assign('unknownTodo', true);
		}
		
		return [
			'template' => WCF::getTPL()->fetch('toDoPreview'),
			'todoID' => $todoID
		];
	}
	
	/**
	 * Validates userID and todoID
	 *
	 */
	public function validateParticipate() {
		$this->readInteger('objectID');
		$this->readInteger('userID');

		if ($this->parameters['objectID'] == 0)
			throw new IllegalLinkException();
		$object = new ToDo($this->parameters['objectID']);
		if ($object === null)
			throw new IllegalLinkException();
		if (in_array($this->parameters['userID'], $object->getResponsibleIDs()))
			throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.toDo.task.participate.alreadyAssigned'));
		if (!$object->canParticipate())
			throw new PermissionDeniedException();
		if (!new User($this->parameters['userID']) === null)
			throw new PermissionDeniedException();
	}
	
	/**
	 * Adds the current user to the responsibles
	 * 
	 */
	public function participate() {
		$this->readInteger('objectID');
		$this->readInteger('userID');
		
		$user = new User($this->parameters['userID']);
		
		$sql = "INSERT INTO wcf" . WCF_N . "_todo_to_user
			(toDoID, userID, username)
			VAlUES(?, ?, ?);";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->parameters['objectID'], $user->userID, $user->username]);
		
		AssignCacheBuilder::getInstance()->reset();
		
		return ['submitted' => 1];
	}
	
	/**
	 * Validates userID and todoID
	 *
	 */
	public function validateEditStatus() {
		$this->readInteger('objectID');
		$this->readInteger('userID');

		if ($this->parameters['objectID'] == 0)
			throw new IllegalLinkException();
		$object = new ToDo($this->parameters['objectID']);
		if ($object === null)
			throw new IllegalLinkException();
		if (!$object->canEditStatus())
			throw new PermissionDeniedException();
		if (!new User($this->parameters['userID']) === null)
			throw new PermissionDeniedException();
	}
	
	/**
	 * Adds the current user to the responsibles
	 * 
	 */
	public function editStatus() {
		$this->readInteger('objectID');
		$this->readInteger('userID');
		$this->readInteger('status');

		$object = new ToDo($this->parameters['objectID']);
		
		$todoAction = new self([$object], 'update', ['data' => ['statusID' => $this->parameters['status']]]);
		$todoAction->executeAction();
		
		return ['success' => 1];
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateSaveFullQuote() {
		$this->todo = $this->getSingleObject();
	}
	
	/**
	 * @inheritDoc
	 */
	public function saveFullQuote() {
		$quoteID = MessageQuoteManager::getInstance()->addQuote(
			'de.mysterycode.wcf.toDo',
			null,
			$this->todo->todoID,
			$this->todo->getExcerpt(),
			$this->todo->getMessage()
			);
		
		if ($quoteID === false) {
			$removeQuoteID = MessageQuoteManager::getInstance()->getQuoteID('de.mysterycode.wcf.toDo', $this->todo->todoID, $this->todo->getExcerpt(), $this->todo->getMessage());
			MessageQuoteManager::getInstance()->removeQuote($removeQuoteID);
		}
		
		$returnValues = [
			'count' => MessageQuoteManager::getInstance()->countQuotes(),
			'fullQuoteMessageIDs' => MessageQuoteManager::getInstance()->getFullQuoteObjectIDs(['de.mysterycode.wcf.toDo'])
		];
		
		if ($quoteID) {
			$returnValues['renderedQuote'] = MessageQuoteManager::getInstance()->getQuoteComponents($quoteID);
		}
		
		return $returnValues;
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateSaveQuote() {
		$this->readString('message');
		$this->readBoolean('renderQuote', true);
		$this->todo = $this->getSingleObject();
	}
	
	/**
	 * @inheritDoc
	 */
	public function saveQuote() {
		$quoteID = MessageQuoteManager::getInstance()->addQuote('de.mysterycode.wcf.toDo', null, $this->todo->todoID, $this->parameters['message'], false);
		
		$returnValues = [
			'count' => MessageQuoteManager::getInstance()->countQuotes(),
			'fullQuoteMessageIDs' => MessageQuoteManager::getInstance()->getFullQuoteObjectIDs(['de.mysterycode.wcf.toDo'])
		];
		
		if ($this->parameters['renderQuote']) {
			$returnValues['renderedQuote'] = MessageQuoteManager::getInstance()->getQuoteComponents($quoteID);
		}
		
		return $returnValues;
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateGetRenderedQuotes() {
		$this->readInteger('parentObjectID');
	}
	
	/**
	 * @inheritDoc
	 */
	public function getRenderedQuotes() {
		$quotes = MessageQuoteManager::getInstance()->getQuotesByParentObjectID('de.mysterycode.wcf.toDo', $this->todo->todoID);
		
		return [
			'template' => implode("\n\n", $quotes)
		];
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateBeginEdit() {
		$this->parameters['objectID'] = (isset($this->parameters['objectID'])) ? intval($this->parameters['objectID']) : 0;
		if (!$this->parameters['objectID']) {
			throw new UserInputException('objectID');
		} else {
			$this->todo = new ToDo($this->parameters['objectID']);
			if (!$this->todo->todoID)
				throw new UserInputException('objectID');
			if (!$this->todo->canEdit())
				throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function beginEdit() {
		BBCodeHandler::getInstance()->setDisallowedBBCodes(explode(',', WCF::getSession()->getPermission('user.message.disallowedBBCodes')));
		
		WCF::getTPL()->assign([
			'todo' => $this->todo,
			'wysiwygSelector' => 'editor'.$this->todo->todoID
		]);
		
		return [
			'actionName' => 'beginEdit',
			'template' => WCF::getTPL()->fetch('todoInlineEditor')
		];
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateSave() {
		if (!isset($this->parameters['data']) || !isset($this->parameters['data']['message']))
			throw new UserInputException('message');
		
		if (empty($this->parameters['data']['message']))
			throw new UserInputException('message', WCF::getLanguage()->get('wcf.global.form.error.empty'));
		
		$this->validateBeginEdit();

		$parameters['removeQuoteIDs'] = (isset($parameters['removeQuoteIDs']) && is_array($parameters['removeQuoteIDs'])) ? ArrayUtil::trim($parameters['removeQuoteIDs']) : [];
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		$todoData = [
			'message' => MessageUtil::stripCrap($this->parameters['data']['message'])
		];
		
		$todoAction = new self([$this->todo], 'update', ['data' => $todoData]);
		$todoAction->executeAction();
		
		if (isset($this->parameters['removeQuoteIDs']) && !empty($this->parameters['removeQuoteIDs']))
			MessageQuoteManager::getInstance()->markQuotesForRemoval($this->parameters['removeQuoteIDs']);
		MessageQuoteManager::getInstance()->removeMarkedQuotes();
		
		$todo = new ToDo($this->todo->todoID);
		
		MessageEmbeddedObjectManager::getInstance()->loadObjects('de.mysterycode.wcf.toDo', [$todo->todoID]);
		
		$data = [
			'actionName' => 'save',
			'message' => $todo->getFormattedMessage()
		];
		
		return $data;
	}
	
	public function validatePrepareProgressUpdate() {
		$todoID = reset($this->objectIDs);
		
		if (empty($todoID))
			throw new UserInputException('objectID');
		
		$this->todo = new ToDo($todoID);
		
		if (!$this->todo->canEdit())
			throw new PermissionDeniedException();
	}
	
	public function prepareProgressUpdate() {
		$todoID = reset($this->objectIDs);
		$this->todo = new ToDo($todoID);
		
		// return template
		return [
			'template' => WCF::getTPL()->fetch('todoUpdateProgress', 'wcf', [
				'todo' => $this->todo
			])
		];
	}
	
	public function validateProgressUpdate() {
		$this->validatePrepareProgressUpdate();
		
		if (!isset($this->parameters['progress']))
			throw new UserInputException('progress');
		
		if ($this->parameters['progress'] < 0 || $this->parameters['progress'] > 100)
			throw new UserInputException('progress', 'invalid');
	}
	
	public function progressUpdate() {
		$todoID = reset($this->objectIDs);
		$this->todo = new ToDo($todoID);
		
		$todoAction = new self([$this->todo], 'update', ['data' => ['progress' => intval($this->parameters['progress'])]]);
		$todoAction->executeAction();
		
		return [
			'progress' => $this->parameters['progress']
		];
	}
}
