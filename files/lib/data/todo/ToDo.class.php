<?php

namespace wcf\data\todo;

use wcf\data\attachment\GroupedAttachmentList;
use wcf\data\category\Category;
use wcf\data\todo\assigned\AssignedCache;
use wcf\data\todo\category\TodoCategory;
use wcf\data\todo\category\TodoCategoryCache;
use wcf\data\todo\status\TodoStatus;
use wcf\data\todo\status\TodoStatusCache;
use wcf\data\user\group\UserGroup;
use wcf\data\user\TodoUserCache;
use wcf\data\user\User;
use wcf\data\DatabaseObject;
use wcf\data\IMessage;
use wcf\data\ITitledLinkObject;
use wcf\data\TUserContent;
use wcf\system\bbcode\AttachmentBBCode;
use wcf\system\cache\builder\UserGroupCacheBuilder;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a todo.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 *
 * @property-read integer todoID ID of this todo-object
 * @property-read string  title subject of the todo
 * @property-read string  description description/content of the todo
 * @property-read string  note notes for this todo
 * @property-read integer statusID id of the status of this todo
 * @property-read integer submitter userid of the user who submitted this todo
 * @property-read string  username username of the user who submitted this todo
 * @property-read integer time timestamp the todo has been created
 * @property-read integer endTime timestamp until the todo has to be solved
 * @property-read boolean private 1 if this todo should be hidden from others, 0 if it's public
 * @property-read integer comments count of comments
 * @property-read integer important 1 if this todos prio is important, 0 if it's medium and -1 id it's low
 * @property-read integer categoryID id of the category this todo is located in
 * @property-read integer updatetimestamp timestamp of the latest update of the todo
 * @property-read integer progress progress (percent) as positive integer
 * @property-read integer remembertime a notification should be created at this time to remember the user of it's not done
 * @property-read boolean enableComments enable the comment feature for this todo
 * @property-read boolean isDisabled 1 if the todo is disabled, 0 otherwise
 * @property-read boolean isDeleted 1 if the todo is trashed, 0 otherwise
 * @property-read integer deleteTime timestamp the todo has been trashed
 * @property-read integer deletedByID userid of the user who trashed the todo
 * @property-read string  deletedBy username of the user who trashed the todo
 * @property-read string  deleteReason reason the todo got trashed for
 * @property-read integer cumulativeLikes cumulative count of likes
 * @property-read integer attachments count of attachments within this todo
 * @property-read boolean hasEmbeddedObjects decription field has embedded objects if set to 1
 * @property-read boolean notesHasEmbeddedObjects notes field has embedded objects of set to 1
 * @property-read string  ipAddress ipaddress of the user who submitted the todo
 * @property-read boolean hasLabels todo has labels if set to 1
 */
class ToDo extends DatabaseObject implements ITitledLinkObject, IRouteController, IMessage {
	use TUserContent;

	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'todo';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'todoID';

	/**
	 * list of responsible user ids
	 *
	 * @var array<integer>
	 */
	protected $responsibleIDs = [];
	
	/**
	 * list of responsible group ids
	 *
	 * @var array<integer>
	 */
	protected $responsibleGroupIDs = [];

	/**
	 * true if embedded object have been loaded already
	 * @var boolean
	 */
	public $embeddedObjectsLoaded = false;

	/**
	 * @var \wcf\data\todo\status\TodoStatus
	 */
	public $status = null;

	/**
	 * @var \wcf\data\todo\category\TodoCategory
	 */
	public $category = null;

	/**
	 * @var \wcf\data\user\User
	 */
	public $user = null;

	public function getResponsibleIDs() {
		if (empty($this->responsibleIDs)) {
			$responsibleArray = AssignedCache::getInstance()->getUsersByTodo($this->todoID);
			
			if (!empty($responsibleArray)) {
				foreach ($responsibleArray as $user)
					$this->responsibleIDs[] = $user->userID;
			}
		}
		
		return $this->responsibleIDs;
	}
	
	public function getResponsibleGroupIDs() {
		if (empty($this->responsibleGroupIDs)) {
			$responsibleArray = AssignedCache::getInstance()->getGroupsByTodo($this->todoID);
			
			if (!empty($responsibleArray)) {
				foreach ($responsibleArray as $group)
					$this->responsibleGroupIDs[] = $group->groupID;
			}
		}
		
		return $this->responsibleGroupIDs;
	}
	
	/**
	 * Returns a list of todos.
	 *
	 * @param array $todoIDs        	
	 * @return array<\wcf\data\todo\ToDo>
	 */
	public static function getToDos(array $todoIDs) {
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add("todo_table.todoID IN (?)", [$todoIDs]);
		$todoList->readObjects();
		
		return $todoList->getObjects();
	}
	
	/**
	 *
	 * @inheritDoc
	 */
	public static function getDatabaseTableAlias() {
		return 'todo_table';
	}
	
	/**
	 *
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->title;
	}
	
	public function getFormattedResponsibles() {
		$string = '';
		foreach ($this->getResponsibleIDs() as $responsible) {
			$user = TodoUserCache::getInstance()->getUser($responsible);
			if ($user->username != '') {
				$string .= $user->username . ', ';
			}
		}
		return substr($string, 0, -2);
	}
	
	public function getFormattedResponsibleGroups() {
		$string = '';
		$groupIDs = $this->getResponsibleGroupIDs();
		foreach ($groupIDs as $groupID) {
			$group = new UserGroup($groupID);
			if ($group->getName() != '') {
				$string .= $group->getName() . ', ';
			}
		}
		return substr($string, 0, -2);
	}
	
	public function getHtmlFormattedResponsibles() {
		$string = '';
		foreach ($this->getResponsibleIDs() as $responsible) {
			$user = TodoUserCache::getInstance()->getUser($responsible);
			if ($user) {
				$string .= '<a href="' . LinkHandler::getInstance()->getLink('User', ['application' => 'wcf', 'object' => $user]) . '" class="userlink" data-user-id="' . $user->userID . '">' . StringUtil::encodeHTML($user->username) . '</a>, ';
			}
		}
		return substr($string, 0, -2);
	}
	
	public function getHtmlFormattedResponsibleGroups() {
		return $this->getFormattedResponsibleGroups();
	}
	
	public function getResponsibles() {
		$responsibleIDs = $this->getResponsibleIDs();
		if (empty($responsibleIDs))
			return [];
		
		$responsibleList = [];
		foreach ($responsibleIDs as $responsible) {
			$responsibleList[] = TodoUserCache::getInstance()->getUser($responsible);
		}
		return $responsibleList;
	}
	
	public function getResponsibleGroups() {
		$groupIDs = $this->getResponsibleGroupIDs();
		if (empty($groupIDs))
			return [];
		
		$responsibleGroupList = [];
		$groupCache = UserGroupCacheBuilder::getInstance()->getData();
		$groupCacheSort = [];
		
		foreach ($groupCache['groups'] as $group)
			$groupCacheSort[$group->groupID] = $group;
		
		foreach ($groupIDs as $groupID) {
			if (!empty($groupCacheSort[$groupID]))
				$responsibleGroupList[] = $groupCacheSort[$groupID];
			else
				$responsibleGroupList[] = new UserGroup($groupID);
		}
		
		return $responsibleGroupList;
	}
	
	public function getResponsiblePreview() {
		$groups = AssignedCache::getInstance()->getGroupsByTodo($this->todoID);
		$users = AssignedCache::getInstance()->getUsersByTodo($this->todoID);
		$preview = [];
		
		$groups = array_slice($groups, 0, 5);
		$groupCache = UserGroupCacheBuilder::getInstance()->getData();
		$groupCacheSort = [];
		
		foreach ($groupCache['groups'] as $group)
			$groupCacheSort[$group->groupID] = $group;
		
		foreach ($groups as $group) {
			$preview[] = str_replace('%s', $groupCacheSort[$group->groupID]->getName(), $groupCacheSort[$group->groupID]->userOnlineMarking);
		}
		
		$users = array_slice($users, 0, 5 - count($groups));
		/** @var \wcf\data\todo\assigned\user\AssignedUser $user */
		foreach ($users as $user) {
			$preview[] = '<a href="' . LinkHandler::getInstance()->getLink('User', array('id' => $user->userID, 'title' => $user->username)) . '" class="userLink" data-user-id="' . $user->userID . '">' . $user->username . '</a>';
		}
		
		return $preview;
	}
	
	public function getStatus() {
		if (!$this->statusID)
			return null;
		
		if (empty($this->status))
			$this->status = TodoStatusCache::getInstance()->getStatus($this->statusID);
		
		if (empty($this->status))
			$this->status = new TodoStatus($this->statusID);
		
		return $this->status;
	}
	
	public function getCategory() {
		if (empty($this->categoryID))
			return null;
		
		if (empty($this->category))
			$this->category = TodoCategoryCache::getInstance()->getCategory($this->categoryID);
		
		if (empty($this->category))
			$this->category = new TodoCategory(new Category($this->categoryID));
		
		return $this->category;
	}

	/**
	 * @inheritDoc
	 */
	public function getMessage() {
		return $this->description;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getFormattedMessage() {
		$this->loadEmbeddedObjects();

		$processor = new HtmlOutputProcessor();
		$processor->process($this->description, 'de.mysterycode.wcf.toDo', $this->todoID);

		return $processor->getHtml();
	}

	/**
	 * @see ToDo::getFormattedMessage()
	 * @deprecated use ToDo::getFormattedMessage()
	 */
	public function getFormattedDescription() {
		return $this->getFormattedMessage();
	}

	public function getFormattedNote() {
		$this->loadEmbeddedObjects();

		$processor = new HtmlOutputProcessor();
		$processor->process($this->note, 'de.mysterycode.wcf.toDo.notes', $this->todoID);

		return $processor->getHtml();
	}

	/**
	 * Returns a simplified version of the formatted message.
	 *
	 * @return	string
	 */
	public function getSimplifiedFormattedMessage() {
		$processor = new HtmlOutputProcessor();
		$processor->setOutputType('text/simplified-html');
		$processor->process($this->description, 'de.mysterycode.wcf.toDo', $this->todoID);

		return $processor->getHtml();
	}

	/**
	 * @see ToDo::getSimplifiedFormattedMessage()
	 */
	public function getSimplifiedFormattedNotes() {
		$processor = new HtmlOutputProcessor();
		$processor->setOutputType('text/simplified-html');
		$processor->process($this->note, 'de.mysterycode.wcf.toDo.notes', $this->todoID);

		return $processor->getHtml();
	}

	/**
	 * @inheritDoc
	 */
	public function getExcerpt($maxLength = 255) {
		return StringUtil::truncateHTML($this->getSimplifiedFormattedMessage(), $maxLength);
	}
	
	/**
	 * @inheritDoc
	 */
	public function isVisible() {
		return $this->canEnter();
	}
	
	/**
	 * @inheritDoc
	 */
	public function __toString() {
		return $this->getFormattedMessage();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getUserID() {
		return $this->submitter;
	}
	
	public function getUser() {
		if (empty($this->user))
			$this->user = TodoUserCache::getInstance()->getUser($this->submitter);
		
		if (empty($this->user))
			$this->user = new User($this->submitter);
		
		return $this->user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('Todo', [
			'application' => 'wcf',
			'object' => $this
		]);
	}

	/**
	 * Returns a list of the ids of accessible todos.
	 *
	 * @param    array $permissions filters boards by given permissions
	 * @return array <integer>
	 * @throws \wcf\system\exception\SystemException
	 */
	public static function getAccessibleTodoIDs($permissions = ['canView', 'canEnter']) {
		$todoIDs = [];
		foreach (ToDoCache::getInstance()->getTodos() as $todo) {
			$result = true;
			foreach ($permissions as $permission) {
				$result = $result && $todo->$permission();
			}
			
			if ($result) {
				$todoIDs[] = $todo->todoID;
			}
		}
		
		return $todoIDs;
	}
	
	public function canEnter() {
		if ($this->isDisabled && !$this->canModerate())
			return false;
		if ($this->isDeleted && !$this->canModerate())
			return false;
		if (!$this->private && $this->getCategory()->canEnterTodos())
			return true;
		if ($this->private && $this->submitter == WCF::getUser()->userID)
			return true;
		if ($this->private && WCF::getSession()->getPermission('user.toDo.toDo.canViewPrivate'))
			return true;
		if ($this->isResponsible())
			return true;
		
		return false;
	}
	
	public function canEdit() {
		if ($this->getCategory()->getPermission('mod.canEditTodos'))
			return true;
		if ($this->getCategory()->getPermission('user.canEditOwnTodos') && $this->submitter == WCF::getUser()->userID)
			return true;
		if ($this->getCategory()->getPermission('user.canEditAssignedTodos') && $this->isResponsible())
			return true;
		
		return false;
	}
	
	public function isResponsible() {
		$responsibleUsers = $this->getResponsibleIDs() ?: [];
		$responsibleGroups = $this->getResponsibleGroupIDs() ?: [];
		foreach (WCF::getUser()->getGroupIDs() as $groupID) {
			if (in_array($groupID, $responsibleGroups))
				return true;
		}
		
		if (in_array(WCF::getUser()->userID, $responsibleUsers))
			return true;
		
		return false;
	}
	
	public function canDelete() {
		if ($this->getCategory()->getPermission('mod.canDeleteTodos'))
			return true;
		if ($this->getCategory()->getPermission('user.canDeleteOwnTodos') && $this->submitter == WCF::getUser()->userID)
			return true;
		if ($this->getCategory()->getPermission('user.canDeleteAssignedTodos') && $this->isResponsible())
			return true;
		
		return false;
	}
	
	public function canEnable() {
		if ($this->getCategory()->getPermission('mod.canEnableTodos'))
			return true;
	
		return false;
	}
	
	public function canDeleteCompletely() {
		if ($this->getCategory()->getPermission('mod.canDeleteTodos'))
			return true;
	
		return false;
	}
	
	public function canRestore() {
		if ($this->getCategory()->getPermission('mod.canRestoreTodos'))
			return true;
	
		return false;
	}
	
	public function canEditStatus() {
		if ($this->getCategory()->getPermission('user.canEditStatus') && $this->canEdit())
			return true;
		
		return false;
	}
	
	/**
	 * @deprecated use ToDo::canEditResponsibles()
	 */
	public function canEditResponsible() {
		return $this->canEditResponsibles();
	}
	
	public function canEditResponsibles() {
		if ($this->getCategory()->canEditResponsibles() && $this->canEdit())
			return true;
		
		return false;
	}
	
	public function canParticipate() {
		$responsibleIDs = $this->getResponsibleIDs();
		if (WCF::getUser()->userID == 0)
			return false;
		if (!empty($responsibleIDs) && in_array(WCF::getUser()->userID, $responsibleIDs))
			return false;
		if ($this->getCategory()->getPermission('user.canParticipate'))
			return true;
		
		return false;
	}
	
	public function canView() {
		return $this->canEnter() || ($this->categoryID !== null && $this->getCategory()->canViewTodos() && $this->private == 0);
	}
	
	public function canViewResponsibleUsers() {
		return $this->categoryID !== null && $this->getCategory()->canViewResponsibleUsers();
	}
	
	public function canViewResponsibleGroups() {
		return $this->categoryID !== null && $this->getCategory()->canViewResponsibleGroups();
	}
	
	public function canViewReminder() {
		return $this->categoryID !== null && $this->getCategory()->canViewReminder();
	}
	
	public function canEditReminder() {
		return $this->categoryID !== null && $this->getCategory()->canEditReminder();
	}
	
	public function canViewDeadline() {
		return $this->categoryID !== null && $this->getCategory()->canViewDeadline();
	}
	
	public function canEditDeadline() {
		return $this->categoryID !== null && $this->getCategory()->canEditDeadline();
	}
	
	public function canEditPriority() {
		return $this->categoryID !== null && $this->getCategory()->canEditPriority();
	}
	
	public function canModerate() {
		return $this->categoryID !== null && $this->getCategory()->isModerator();
	}
	
	public function canEditProgress() {
		return $this->canEdit() || $this->canModerate();
	}

	/**
	 * Returns and assigns embedded attachments.
	 *
	 * @return	GroupedAttachmentList
	 */
	public function getAttachments() {
		if (MODULE_ATTACHMENT == 1 && $this->attachments) {
			$attachmentList = new GroupedAttachmentList('de.mysterycode.wcf.toDo.toDo');
			$attachmentList->getConditionBuilder()->add('attachment.objectID IN (?)', [$this->todoID]);
			$attachmentList->readObjects();
			$attachmentList->setPermissions([
				'canDownload' => $this->isVisible(),
				'canViewPreview' => $this->isVisible()
			]);

			/** @noinspection PhpDeprecationInspection */
			// set embedded attachments
			AttachmentBBCode::setAttachmentList($attachmentList);

			return $attachmentList;
		}

		return null;
	}

	/**
	 * Loads the embedded objects.
	 */
	public function loadEmbeddedObjects() {
		if (($this->hasEmbeddedObjects || $this->notesHasEmbeddedObjects) && !$this->embeddedObjectsLoaded) {
			MessageEmbeddedObjectManager::getInstance()->loadObjects('de.mysterycode.wcf.toDo', [$this->todoID]);
			MessageEmbeddedObjectManager::getInstance()->loadObjects('de.mysterycode.wcf.toDo.notes', [$this->todoID]);
			$this->embeddedObjectsLoaded = true;
		}
	}
}
