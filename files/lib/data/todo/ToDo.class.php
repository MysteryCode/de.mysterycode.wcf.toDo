<?php

namespace wcf\data\todo;
use wcf\data\attachment\Attachment;
use wcf\data\attachment\GroupedAttachmentList;
use wcf\data\category\Category;
use wcf\data\DatabaseObject;
use wcf\data\IMessage;
use wcf\data\todo\assigned\AssignedCache;
use wcf\data\todo\category\TodoCategory;
use wcf\data\todo\category\TodoCategoryCache;
use wcf\data\todo\status\TodoStatus;
use wcf\data\todo\status\TodoStatusCache;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\system\bbcode\AttachmentBBCode;
use wcf\system\bbcode\MessageParser;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\cache\builder\UserGroupCacheBuilder;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a todo.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDo extends DatabaseObject implements IBreadcrumbProvider, IRouteController, IMessage {
	/**
	 *
	 * @see \wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'todo';
	
	/**
	 *
	 * @see \wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'todoID';

	/**
	 * list of responsible user ids
	 *
	 * @var array<integer>
	 */
	protected $responsibleIDs = null;
	
	/**
	 * list of responsible group ids
	 *
	 * @var array<integer>
	 */
	protected $responsibleGroupIDs = null;
	
	public $status = null;
	public $category = null;
	
	public $enableSmilies = true;
	public $enableHtml = false;
	public $enableBBCodes = true;
	
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
		$todoList->getConditionBuilder()->add("todo_table.todoID IN (?)", array($todoIDs));
		$todoList->readObjects();
		
		return $todoList->getObjects();
	}
	
	/**
	 *
	 * @see \wcf\data\IStorableObject::getDatabaseTableAlias()
	 */
	public static function getDatabaseTableAlias() {
		return 'todo_table';
	}
	
	/**
	 *
	 * @see \wcf\system\request\IRouteController::getTitle()
	 */
	public function getTitle() {
		return $this->title;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getFormattedResponsibles() {
		$string = '';
		foreach ($this->getResponsibleIDs() as $responsible) {
			$user = new User($responsible);
			if ($user->username != '') {
				$string .= $user->username . ', ';
			}
		}
		return substr($string, 0, -2);
	}
	
	public function getFormattedResponsibleGroups() {
		$string = '';
		foreach ($this->getResponsibleGroupIDs() as $groupID) {
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
			$user = new User($responsible);
			if ($user) {
				$string .= '<a href="' . LinkHandler::getInstance()->getLink('User', array('application' => 'wcf', 'object' => $user)) . '" class="userlink" data-user-id="' . $user->userID . '">' . StringUtil::encodeHTML($user->username) . '</a>, ';
			}
		}
		return substr($string, 0, -2);
	}
	
	public function getHtmlFormattedResponsibleGroups() {
		return $this->getFormattedResponsibleGroups();
	}
	
	public function getResponsibles() {
		if (empty($this->getResponsibleIDs()))
			return array();
		
		$responsibleList = array();
		foreach ($this->getResponsibleIDs() as $responsible) {
			$responsibleList[] = new User($responsible);
		}
		return $responsibleList;
	}
	
	public function getResponsibleGroups() {
		$groupIDs = $this->getResponsibleGroupIDs();
		if (empty($groupIDs))
			return array();
		
		$responsibleGroupList = array();
		foreach ($groupIDs as $groupID) {
			$responsibleGroupList[] = new UserGroup($groupID);
		}
		return $responsibleGroupList;
	}
	
	public function getResponsiblePreview() {
		$groups = AssignedCache::getInstance()->getGroupsByTodo($this->todoID);
		$users = AssignedCache::getInstance()->getUsersByTodo($this->todoID);
		$preview = array();
		
		$groups = array_slice($groups, 0, 5);
		$groupCache = UserGroupCacheBuilder::getInstance()->getData();
		$groupCacheSort = array();
		
		foreach ($groupCache['groups'] as $group)
			$groupCacheSort[$group->groupID] = $group;
		
		foreach ($groups as $group) {
			$preview[] = str_replace('%s', $groupCacheSort[$group->groupID]->getName(), $groupCacheSort[$group->groupID]->userOnlineMarking);
		}
		
		$users = array_slice($users, 0, 5 - count($groups));
		foreach ($users as $user) {
			$preview[] = "<a href=\"{link controller='User' id=" . $user->userID . "}{/link}\" class=\"userLink\" data-user-id=\"" . $user->userID . "}\">" . $user->username . "</a>";
		}
		
		return $preview;
	}
	
	public function getFormattedDescription() {
		AttachmentBBCode::setObjectID($this->todoID);
		
		// parse and return message
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->description, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}
	
	public function getFormattedNote() {
		// parse and return message
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->note, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
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
	 * @see	\wcf\system\breadcrumb\IBreadcrumbProvider::getBreadcrumb()
	 */
	public function getBreadcrumb() {
		return new Breadcrumb($this->getTitle(), $this->getLink());
	}
	
	public function getBreadcrumbs() {
		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('wcf.header.menu.toDo'), LinkHandler::getInstance()->getLink('ToDoList', array())));
		if ($this->getCategory())
			WCF::getBreadcrumbs()->add($this->getCategory()->getBreadCrumb());
	}
	
	/**
	 * @see	\wcf\data\IMessage::getFormattedMessage()
	 */
	public function getFormattedMessage() {
		AttachmentBBCode::setObjectID($this->todoID);
		
		// parse and return message
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->getMessage(), $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}
	
	/**
	 * @see	\wcf\data\IMessage::getMessage()
	 */
	public function getMessage() {
		return $this->description;
	}
	
	/**
	 * @see	\wcf\data\IMessage::isVisible()
	 */
	public function isVisible() {
		return $this->canEnter();
	}
	
	/**
	 * @see	\wcf\data\IMessage::__toString()
	 */
	public function __toString() {
		return $this->getFormattedMessage();
	}
	
	/**
	 * @see	\wcf\data\IUserContent::getTime()
	 */
	public function getTime() {
		return $this->timestamp;
	}
	
	/**
	 * @see	\wcf\data\IUserContent::getUserID()
	 */
	public function getUserID() {
		return $this->submitter;
	}
	
	/**
	 * @see	\wcf\data\IUserContent::getUsername()
	 */
	public function getUsername() {
		return $this->getUser()->username;
	}
	
	/**
	 * @see	\wcf\data\IMessage::getExcerpt()
	 */
	public function getExcerpt($maxLength = 255) {
		$text = $this->getFormattedMessage();
		return StringUtil::truncateHTML($text, 255);
// 		if (strlen($text) > 255)
// 			return nl2br(mb_substr(StringUtil::stripHTML($text), 0, 255)).' ...';
// 		else
// 			return nl2br(StringUtil::stripHTML($text));
	}
	
	public function getAttachments() {
		if (MODULE_ATTACHMENT == 1 && $this->attachments) {
			$attachmentList = new GroupedAttachmentList('de.mysterycode.wcf.toDo.toDo');
			$attachmentList->getConditionBuilder()->add('attachment.objectID IN (?)', array($this->todoID));
			$attachmentList->readObjects();
			$attachmentList->setPermissions(array(
				'canDownload' => WCF::getSession()->getPermission('user.toDo.attachment.canDownloadAttachments'),
				'canViewPreview' => WCF::getSession()->getPermission('user.toDo.attachment.canDownloadAttachments')
			));
			
			AttachmentBBCode::setAttachmentList($attachmentList);
			return $attachmentList;
		}
		return null;
	}
	
	public function getUser() {
		return new User($this->submitter);
	}
	
	public function getLink() {
		return LinkHandler::getInstance()->getLink('ToDo', array(
			'application' => 'wcf',
			'object' => $this
		));
	}
	
	public function canEnter() {
		if ($this->isDisabled && !$this->canModerate())
			return false;
		if ($this->isDeleted && !$this->canModerate())
			return false;
		if ($this->private == 0 && $this->getCategory()->canEnterTodos())
			return true;
		if ($this->private == 1 && $this->submitter == WCF::getUser()->userID)
			return true;
		if (in_array(WCF::getUser()->userID, $this->getResponsibleIDs()))
			return true;
		
		return false;
	}
	
	public function canEdit() {
		if ($this->getCategory()->getPermission('mod.canEdit'))
			return true;
		if ($this->getCategory()->getPermission('user.canEditOwnTodos') && $this->submitter == WCF::getUser()->userID)
			return true;
		if ($this->getCategory()->getPermission('user.canEditAssigned') && in_array(WCF::getUser()->userID, $this->getResponsibleIDs()))
			return true;
		
		return false;
	}
	
	public function canDelete() {
		if ($this->getCategory()->getPermission('mod.canDelete'))
			return true;
		if ($this->getCategory()->getPermission('user.canDeleteOwnTodos') && $this->submitter == WCF::getUser()->userID)
			return true;
		if ($this->getCategory()->getPermission('user.canDeleteAssigned') && in_array(WCF::getUser()->userID, $this->getResponsibleIDs()))
			return true;
		
		return false;
	}
	
	public function canEnable() {
		if ($this->getCategory()->getPermission('mod.canEnable'))
			return true;
	
		return false;
	}
	
	public function canDeleteCompletely() {
		if ($this->getCategory()->getPermission('mod.canDelete'))
			return true;
	
		return false;
	}
	
	public function canRestore() {
		if ($this->getCategory()->getPermission('mod.canRestore'))
			return true;
	
		return false;
	}
	
	public function canEditStatus() {
		if ($this->getCategory()->getPermission('user.status.canEditOwn') && $this->submitter == WCF::getUser()->userID)
			return true;
		if ($this->getCategory()->getPermission('user.status.canEditAssigned') && !empty($this->getResponsibleIDs()) && in_array(WCF::getUser()->userID, $this->getResponsibleIDs()))
			return true;
		if ($this->getCategory()->getPermission('mod.status.canEdit'))
			return true;
		
		return false;
	}
	
	public function canEditResponsible() {
		if ($this->getCategory()->getPermission('user.responsible.canEditOwn') && $this->submitter == WCF::getUser()->userID)
			return true;
		if ($this->getCategory()->getPermission('user.responsible.canEditAssigned') && !empty($this->getResponsibleIDs()) && in_array(WCF::getUser()->userID, $this->getResponsibleIDs()))
			return true;
		if ($this->getCategory()->getPermission('mod.responsible.canEdit'))
			return true;
		
		return false;
	}
	
	public function canParticipate() {
		if (WCF::getUser()->userID == 0)
			return false;
		if (!empty($this->getResponsibleIDs()) && in_array(WCF::getUser()->userID, $this->getResponsibleIDs()))
			return false;
		if ($this->getCategory()->getPermission('user.responsible.canParticipate'))
			return true;
		
		return false;
	}
	
	public function canViewResponsibleUsers() {
		return $this->getCategory()->canViewResponsibleUsers();
	}
	
	public function canViewResponsibleGroups() {
		return $this->getCategory()->canViewResponsibleGroups();
	}
	
	public function canViewReminder() {
		return $this->getCategory()->canViewReminder();
	}
	
	public function canEditReminder() {
		return $this->getCategory()->canEditReminder();
	}
	
	public function canViewDeadline() {
		return $this->getCategory()->canViewDeadline();
	}
	
	public function canEditDeadline() {
		return $this->getCategory()->canEditDeadline();
	}
	
	public function canEditPriority() {
		return $this->getCategory()->canEditPriority();
	}
	
	public function canModerate() {
		$validPermissions = array(
			'mod.toDo.canEdit',
			'mod.toDo.canDelete',
			'mod.toDo.canEnable'
		);
		
		foreach ($validPermissions as $permission) {
			if (WCF::getSession()->getPermission($permission)) {
				return true;
			}
		}
		
		return false;
	}
}
