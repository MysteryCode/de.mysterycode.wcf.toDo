<?php

namespace wcf\form;
use wcf\data\category\Category;
use wcf\data\todo\category\RestrictedTodoCategoryNodeTree;
use wcf\data\todo\category\TodoCategory;
use wcf\data\todo\status\TodoStatusList;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoAction;
use wcf\data\user\UserProfile;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\object\ToDoUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the toDoAdd form.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoAddForm extends MessageForm {
	/**
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	
	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;
	
	/**
	 * @see	\wcf\form\MessageForm::$attachmentObjectType
	 */
	public $attachmentObjectType = 'de.mysterycode.wcf.toDo.toDo';
	
	public $enableComments = 1;
	
	public $neededModules = array('TODOLIST');
	
	public $description = '';
	public $endTime = 0;
	public $note = '';
	public $responsibles = '';
	public $responsibleGroups = '';
	public $statusID = 0;
	public $title = '';
	public $private = 0;
	public $important = 0;
	public $categoryID = 0;
	public $category = null;
	public $newCategory = '';
	public $progress = 0;
	public $remembertime = 0;
	public $enableSmilies = 0;
	public $enableHtml = 0;
	public $enableBBCodes = 0;

	public $statusList = array();
	
	/**
	 * @see	\wcf\form\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		MessageQuoteManager::getInstance()->readParameters();
		
		if (isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
		$this->category = new TodoCategory(new Category($this->categoryID));
		if (!$this->category->categoryID)
			throw new IllegalLinkException();
	}
	
	/**
	 * @see wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['endTime']) && $_POST['endTime'] > 0 && $_POST['endTime'] != '') $this->endTime = \DateTime::createFromFormat('Y-m-d H:i', $_POST['endTime'], WCF::getUser()->getTimeZone())->getTimestamp();
		if (isset($_POST['note'])) $this->note = StringUtil::trim($_POST['note']);
		if (isset($_POST['statusID'])) $this->statusID = StringUtil::trim($_POST['statusID']);
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['private'])) $this->private = 1;
		if (isset($_POST['priority'])) $this->important = StringUtil::trim($_POST['priority']);
		if (isset($_POST['categoryID'])) $this->categoryID = StringUtil::trim($_POST['categoryID']);
		if (isset($_POST['newCategory'])) $this->newCategory = StringUtil::trim($_POST['newCategory']);
		if (isset($_POST['progress'])) $this->progress = StringUtil::trim($_POST['progress']);
		if (isset($_POST['remembertime']) && $_POST['remembertime'] > 0 && $_POST['remembertime'] != '') $this->remembertime = \DateTime::createFromFormat('Y-m-d', $_POST['remembertime'], WCF::getUser()->getTimeZone())->getTimestamp();
		if (isset($_POST['enableSmilies'])) $this->enableSmilies = 1;
		if (isset($_POST['enableHtml']) && WCF::getSession()->getPermission('user.toDo.canUseHtml')) $this->enableHtml = 1;
		if (isset($_POST['enableBBCodes'])) $this->enableBBCodes = 1;
		if (isset($_POST['responsibles'])) $this->responsibles = StringUtil::trim($_POST['responsibles']);
		if (isset($_POST['responsibleGroups'])) $this->responsibleGroups = StringUtil::trim($_POST['responsibleGroups']);
		
		MessageQuoteManager::getInstance()->readFormParameters();
	}
	
	/**
	 * @see wcf\form\IForm::validate()
	 */
	public function validate() {
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
		
		if (empty($this->description)) {
			throw new UserInputException('description');
		}
		
		if (empty($this->statusID) && TODO_SET_STATUS_ON_CREATE && $this->category->canEditStatus()) {
			throw new UserInputException('statusID');
		}
		
		if (empty($this->progress) && TODO_PROGRESS_ENABLE) {
			$this->progess = 0;
		}
		
		if (($this->progress < 0 || $this->progress > 100) && TODO_PROGRESS_ENABLE) {
			throw new UserInputException('progress', 'inValid');
		}
	}
	
	/**
	 * @see wcf\form\IForm::save()
	 */
	public function save() {
		$todoData = array(
			'data' => array(
				'title' => $this->title,
				'description' => $this->description,
				'note' => $this->note,
				'submitter' => WCF::getUser()->userID,
				'username' => WCF::getUser()->username,
				'timestamp' => TIME_NOW,
				'endTime' => $this->endTime,
				'private' => $this->private,
				'important' => $this->important,
				'categoryID' => $this->categoryID ?: null,
				'progress' => $this->progress,
				'remembertime' => $this->remembertime,
				'enableSmilies' => $this->enableSmilies,
				'enableHtml' => $this->enableHtml,
				'enableBBCodes' => $this->enableBBCodes
			),
			'attachmentHandler' => $this->attachmentHandler
		);
		
		if (!$this->category->getPermission('user.canAddTodoWithoutModeration')) {
			$todoData['data']['isDisabled'] = 1;
		}
		
		if (!empty($this->statusID))
			$todoData['data']['statusID'] = $this->statusID;
		
		$this->objectAction = new ToDoAction(array(), 'create', $todoData);
		$resultValues = $this->objectAction->executeAction();

		if (!empty($this->responsibles)) {
			$responsibleUserAction = new ToDoAction(array($resultValues['returnValues']->todoID), 'updateResponsibles', array('search' => $this->responsibles));
			$responsibleUserAction->executeAction();
		}
		
		if (!empty($this->responsibleGroups)) {
			$responsibleGroupAction = new ToDoAction(array($resultValues['returnValues']->todoID), 'updateResponsibleGroups', array('search' => $this->responsibleGroups));
			$responsibleGroupAction->executeAction();
		}
		
		MessageQuoteManager::getInstance()->saved();
		
		$this->saved();
		
		if ($resultValues['returnValues']->isDisabled && !$this->category->getPermission('mod.canEnableTodos')) {
			HeaderUtil::delayedRedirect($this->category->getLink(), WCF::getLanguage()->get('wcf.toDo.moderation.redirect'), 30);
		} else {
			HeaderUtil::redirect($resultValues['returnValues']->getLink());
		}
		exit;
	}
	
	/**
	 * @see wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		$statusList = new TodoStatusList();
		$statusList->readObjects();
		$this->statusList = $statusList->getObjects();
		
		$categoryNodeTree = new RestrictedTodoCategoryNodeTree();
		$this->categoryList = $categoryNodeTree->getIterator();
	}
	
	/**
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('wcf.header.menu.toDo'), LinkHandler::getInstance()->getLink('ToDoList', array())));
		
		MessageQuoteManager::getInstance()->assignVariables();
		
		WCF::getTPL()->assign( array(
			'title' => $this->title,
			'description' => $this->description,
			'note' => $this->note,
			'statusID' => $this->statusID,
			'responsibles' => $this->responsibles,
			'responsibleGroups' => $this->responsibleGroups,
			'endTime' => $this->endTime,
			'private' => $this->private,
			'important' => $this->important,
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryList' => $this->categoryList,
			'progress' => $this->progress,
			'enableSmilies' => $this->enableSmilies,
			'enableHtml' => $this->enableHtml,
			'enableBBCodes' => $this->enableBBCodes,
			'remembertime' => $this->remembertime,
			'allowedFileExtensions' => explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.toDo.attachment.allowedAttachmentExtensions'))),
			'statusList' => $this->statusList,
			'action' => 'add'
		));
	}
	
	public function updateResponsibles($todoID = 0, $search, $existingResponsibles = array()) {
		if ($todoID == 0)
			return null;
		
		$responsibleList = UserProfile::getUserProfilesByUsername(ArrayUtil::trim(explode(',', $search)));
		
		$responsibleList = array_unique($responsibleList);
		
		$userIDs = array();
		$checkArray = array();
		foreach ($responsibleList as $user) {
			if ($user && $user !== null && !in_array($user->userID, $existingResponsibles)) {
				$userIDs[] = $user->userID;
				$sql = "INSERT INTO wcf" . WCF_N . "_todo_to_user
					(toDoID, userID, username)
					VAlUES(?, ?, ?);";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array($todoID, $user->userID, $user->username));
				
				$checkArray[] = $user->userID;
			}
		}
		
		if ($todoID != 0) {
			foreach ($existingResponsibles as $responsible) {
				if (!in_array($responsible, $checkArray)) {
					$sql = "DELETE FROM wcf" . WCF_N . "_todo_to_user
						WHERE toDoID = ?
							AND userID = ?";
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute(array($todoID, $responsible));
				}
			}
		}
		
		if (!empty($userIDs))
			UserNotificationHandler::getInstance()->fireEvent('assign', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todoID)), $userIDs);
	}
}
