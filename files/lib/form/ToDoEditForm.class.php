<?php

namespace wcf\form;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoAction;
use wcf\data\todo\ToDoCache;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\ILinkableObject;
use wcf\form\AbstractForm;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\request\LinkHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

/**
 * Shows the toDoEdit form.
 *
 * @author Florian Gail
 * @copyright 2014 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoEditForm extends ToDoAddForm {
	/**
	 *
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	public $neededModules = array('TODOLIST');
	public $todo = null;
	public $description = '';
	public $note = '';
	public $responsibles = array();
	public $status = 0;
	public $title = '';
	public $todoID = 0;
	public $endTime = 0;
	public $private = 0;
	public $important = 0;
	public $category = 0;
	public $newCategory = '';
	public $progress = 0;
	public $remembertime = 0;
	public $html_description = 0;
	public $html_notes = 0;
	public $editType = 0;
	public $disableToDo = 0;
	
	/**
	 *
	 * @see wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if(isset($_REQUEST['id'])) $this->todoID = intval($_REQUEST['id']);
		$this->todo = new ToDo($this->todoID);
		if (!$this->todo->id)
			throw new IllegalLinkException();
	}
	
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
		if(isset($_POST['html_description'])) $this->html_description = 1;
		if(isset($_POST['html_notes'])) $this->html_notes = 1;
		if(isset($_POST['responsibles'])) $this->responsibles = StringUtil::trim($_POST['responsibles']);
		
		if($this->newCategory != '' && TODO_CATEGORY_ENABLE) $this->category = $this->createCategory($this->newCategory);
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
				'status' => $this->status,
				'submitter' => WCF::getUser()->userID,
				'timestamp' => TIME_NOW,
				'endTime' => $this->endTime,
				'private' => $this->private,
				'important' => $this->important,
				'category' => $this->category,
				'updatetimestamp' => TIME_NOW,
				'progress' => $this->progress,
				'html_description' => $this->html_description,
				'html_notes' => $this->html_notes,
				'remembertime' => $this->remembertime
			)
		);
		
		if($this->disableToDo) {
			$todoData['data']['isDisabled'] = 1;
		}
		
		$this->objectAction = new ToDoAction( array($this->todo), 'update', $todoData);
		$this->objectAction->executeAction();
		$this->updateResponsibles($this->todo->id, $this->responsibles, $this->todo->getResponsibleIDs());
		$this->saved();
		
		HeaderUtil::redirect($this->todo->getLink());
		exit();
	}
	
	/**
	 *
	 * @see wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->responsibles = $this->todo->getFormattedResponsibles();
		
		if(! $this->checkPermissions())
			throw new PermissionDeniedException();
		
		$this->title = $this->todo->title;
		$this->description = $this->todo->description;
		$this->note = $this->todo->note;
		$this->status = $this->todo->status;
		$this->category = $this->todo->category;
		$this->html_description = $this->todo->html_description;
		$this->html_notes = $this->todo->html_notes;
		$this->progress = $this->todo->progress;
		$this->important = $this->todo->important;
		$this->private = $this->todo->private;
		
		if($this->todo->endTime > 0) {
			$this->endTime = DateUtil::getDateTimeByTimestamp($this->todo->endTime);
			$this->endTime->setTimezone( WCF::getUser()->getTimeZone());
			$this->endTime = $this->endTime->format('c');
		}
		
		if($this->todo->remembertime > 0) {
			$this->remembertime = DateUtil::getDateTimeByTimestamp($this->todo->remembertime);
			$this->remembertime->setTimezone( WCF::getUser()->getTimeZone());
			$this->remembertime = $this->remembertime->format('Y-m-d');
		}
		
		WCF::getBreadcrumbs()->add( new Breadcrumb( WCF::getLanguage()->get('wcf.header.menu.toDo'), LinkHandler::getInstance()->getLink('ToDoList', array())));
		WCF::getBreadcrumbs()->add( new Breadcrumb($this->title, LinkHandler::getInstance()->getLink('ToDo', array(
			'id' => $this->todoID 
		))));
	}
	
	/**
	 *
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign( array(
			'id' => $this->todoID,
			'todo' => $this->todo,
			'toDoCategoryList' => $this->getCategories(),
			'editStatusOnly' => $this->editType,
			'action' => 'edit' 
		));
	}
	
	public function checkPermissions() {
		if(WCF::getSession()->getPermission('user.toDo.status.canEdit'))
			return true;
		if(WCF::getSession()->getPermission('user.toDo.status.canEditAssigned'))
			return true;
		if(WCF::getSession()->getPermission('user.toDo.toDo.canEdit')) {
			$this->editType = 1;
			return true;
		}
		if(WCF::getSession()->getPermission('user.toDo.toDo.canEditOwn') && $item['submitter'] == WCF::getUser()->userID) {
			$this->editType = 1;
			return true;
		}
		if(WCF::getSession()->getPermission('user.toDo.toDo.canEditAssigned') && in_array($this->responsibles, WCF::getUser()->userID)) {
			$this->editType = 1;
			return true;
		}
		
		return false;
	}
}