<?php
namespace wcf\form;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoAction;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\ILinkableObject;
use wcf\form\AbstractForm;
use wcf\form\MessageForm;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\object\ToDoUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

/**
 * Shows the toDoAdd form.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoAddForm extends MessageForm {
	/**
	 *
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	public $enableComments = 1;
	public $disableToDo = false;
	
	/**
	 *
	 * @see \wcf\form\MessageForm::$enableMultilingualism
	 */
	public $enableMultilingualism = true;
	public $neededModules = array('TODOLIST');
	public $neededPermissions = array('user.toDo.toDo.canAdd');
	public $description = '';
	public $endTime = 0;
	public $note = '';
	public $responsibles = '';
	public $status = 1;
	public $title = '';
	public $private = 0;
	public $important = 0;
	public $category = 0;
	public $newCategory = '';
	public $progress = 0;
	public $remembertime = 0;
	public $enableSmilies = 0;
	public $enableHtml = 0;
	public $enableBBCodes = 0;
	public $canEditStatus = 0;
	public $canEditResponsible = 0;
	
	/**
	 *
	 * @see wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if(isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if(isset($_POST['endTime']) && $_POST['endTime'] > 0 && $_POST['endTime'] != '') $this->endTime = \DateTime::createFromFormat('Y-m-d H:i', $_POST['endTime'], WCF::getUser()->getTimeZone())->getTimestamp();
		if(isset($_POST['note'])) $this->note = StringUtil::trim($_POST['note']);
		if(isset($_POST['status'])) $this->status = StringUtil::trim($_POST['status']);
		if(isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if(isset($_POST['private'])) $this->private = 1;
		if(isset($_POST['important'])) $this->important = 1;
		if(isset($_POST['category'])) $this->category = StringUtil::trim($_POST['category']);
		if(isset($_POST['newCategory'])) $this->newCategory = StringUtil::trim($_POST['newCategory']);
		if(isset($_POST['progress'])) $this->progress = StringUtil::trim($_POST['progress']);
		if(isset($_POST['remembertime']) && $_POST['remembertime'] > 0 && $_POST['remembertime'] != '') $this->remembertime = \DateTime::createFromFormat('Y-m-d', $_POST['remembertime'], WCF::getUser()->getTimeZone())->getTimestamp();
		if(isset($_POST['enableSmilies'])) $this->enableSmilies = 1;
		if(isset($_POST['enableHtml']) && WCF::getSession()->getPermission('user.toDo.canUseHtml')) $this->enableHtml = 1;
		if(isset($_POST['enableBBCodes'])) $this->enableBBCodes = 1;
		if(isset($_POST['responsibles'])) $this->responsibles = StringUtil::trim($_POST['responsibles']);
		
		if($this->newCategory != '' && TODO_CATEGORY_ENABLE) $this->category = $this->createCategory($this->newCategory);
	}
	
	/**
	 *
	 * @see wcf\form\IForm::validate()
	 */
	public function validate() {
		if(empty($this->title)) {
			throw new UserInputException('title');
		}
		
		if(empty($this->description)) {
			throw new UserInputException('description');
		}
		
		if(empty($this->status) && TODO_SET_STATUS_ON_CREATE && $this->canEditStatus()) {
			throw new UserInputException('status');
		}
		
		if(empty($this->category) && empty($this->newCategory) && TODO_CATEGORY_ENABLE) {
			throw new UserInputException('category');
		}
	}
	
	/**
	 *
	 * @see wcf\form\IForm::save()
	 */
	public function save() {
		$todoData = array(
			'data' => array(
				'title' => $this->title,
				'description' => $this->description,
				'note' => $this->note,
				'submitter' => WCF::getUser()->userID,
				'timestamp' => TIME_NOW,
				'endTime' => $this->endTime,
				'private' => $this->private,
				'important' => $this->important,
				'category' => $this->category,
				'progress' => $this->progress,
				'remembertime' => $this->remembertime,
				'enableSmilies' => $this->enableSmilies,
				'enableHtml' => $this->enableHtml,
				'enableBBCodes' => $this->enableBBCodes
			) 
		);
		
		if (!WCF::getSession()->getPermission('user.toDo.toDo.canAddWithoutModeration')) {
			$todoData['data']['isDisabled'] = 1;
		}
		
		if($this->canEditStatus()) {
			$todoData['data']['status'] = $this->status;
		}
		
		$this->objectAction = new ToDoAction(array(), 'create', $todoData);
		$resultValues = $this->objectAction->executeAction();
		
		if($this->canEditResponsible()) {
			$this->updateResponsibles($resultValues['returnValues']->id, $this->responsibles);
		}
		
		$this->saved();
		
		if ($resultValues['returnValues']->isDisabled && !WCF::getSession()->getPermission('mod.toDo.canEnable')) {
			HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('ToDoCategory', array('application' => 'wcf', 'id' => $this->category)), WCF::getLanguage()->get('wcf.todo.moderation.redirect'), 30);
		}
		else {
			HeaderUtil::redirect($resultValues['returnValues']->getLink());
		}
		exit;
	}
	
	/**
	 *
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('wcf.header.menu.toDo'), LinkHandler::getInstance()->getLink('ToDoList', array())));
		
		WCF::getTPL()->assign( array(
			'title' => $this->title,
			'description' => $this->description,
			'note' => $this->note,
			'status' => $this->status,
			'responsibles' => $this->responsibles,
			'endTime' => $this->endTime,
			'private' => $this->private,
			'important' => $this->important,
			'toDoCategory' => $this->category,
			'toDoCategoryList' => $this->getCategories(),
			'progress' => $this->progress,
			'enableSmilies' => $this->enableSmilies,
			'enableHtml' => $this->enableHtml,
			'enableBBCodes' => $this->enableBBCodes,
			'remembertime' => $this->remembertime,
			'canEditStatus' => $this->canEditStatus(),
			'canEditResponsible' => $this->canEditResponsible(),
			'action' => 'add' 
		));
	}
	
	public function updateResponsibles($todoID = 0, $search, $existingResponsibles = array()) {
		if($todoID == 0)
			return null;
		
		$responsibleList = UserProfile::getUserProfilesByUsername(ArrayUtil::trim(explode(',', $search)));
		
		$userIDs = array();
		$checkArray = array();
		foreach($responsibleList as $user) {
			if($user && !in_array($user->userID, $existingResponsibles)) {
				$userIDs[] = $user->userID;
				$sql = "INSERT INTO wcf" . WCF_N . "_todo_to_user
					(toDoID, userID, username)
					VAlUES(?, ?, ?);";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array($todoID, $user->userID, $user->username));
			}
			$checkArray[] = $user->userID;
		}
		
		if($todoID != 0) {
			foreach($existingResponsibles as $responsible) {
				if(!in_array($responsible, $checkArray)) {
					$sql = "DELETE FROM wcf" . WCF_N . "_todo_to_user
						WHERE toDoID = ?
							AND userID = ?";
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute(array($todoID, $responsible));
				}
			}
		}
		
		if(!empty($userIDs))
			UserNotificationHandler::getInstance()->fireEvent('assign', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todoID)), $userIDs);
	}
	
	public function createCategory($title) {
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category
			WHERE title = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($title));
		$item = $statement->fetchArray();
		
		if($item)
			return $item['id'];
		
		$sql = "INSERT INTO wcf" . WCF_N . "_todo_category
			(title)
			VAlUES( ?);";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($title));
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category
			WHERE title = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($title));
		$item = $statement->fetchArray();
		
		return $item['id'];
	}
	
	public function getCategories() {
		$categories = array();
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category
			ORDER BY title ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		while($row = $statement->fetchArray()) {
			$categories[] = array(
				'id' => $row["id"],
				'title' => $row["title"] 
			);
		}
		return $categories;
	}
	
	public function canEditStatus() {
		if(WCF::getSession()->getPermission('user.toDo.status.canEditOwn'))
			return true;
		if(WCF::getSession()->getPermission('mod.toDo.status.canEdit'))
			return true;
	
		return false;
	}
	
	public function canEditResponsible() {
		if(WCF::getSession()->getPermission('user.toDo.responsible.canEditOwn'))
			return true;
		if(WCF::getSession()->getPermission('mod.toDo.responsible.canEdit'))
			return true;
	
		return false;
	}
}