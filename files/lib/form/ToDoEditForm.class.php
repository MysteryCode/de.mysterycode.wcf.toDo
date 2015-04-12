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
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoEditForm extends ToDoAddForm {
	public $neededPermissions = array();
	
	public $todo = null;
	public $todoID = 0;
	
	/**
	 *
	 * @see wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->todoID = intval($_REQUEST['id']);
		$this->todo = new ToDo($this->todoID);
		if (!$this->todo->id)
			throw new IllegalLinkException();
		
		$this->attachmentObjectID = $this->todoID;
	}
	
	/**
	 *
	 * @see wcf\form\IForm::save()
	 */
	public function save() {
		$todoData = array('data' => array());
		
		if ($this->todo->canEdit()) {
			$todoData = array(
				'data' => array(
					'title' => $this->title,
					'description' => $this->description,
					'note' => $this->note,
<<<<<<< HEAD
					'submitter' => WCF::getUser()->userID,
=======
>>>>>>> master
					'updatetimestamp' => TIME_NOW,
					'endTime' => $this->endTime,
					'private' => $this->private,
					'important' => $this->important,
					'category' => $this->category,
<<<<<<< HEAD
					'updatetimestamp' => TIME_NOW,
=======
>>>>>>> master
					'progress' => $this->progress,
					'enableSmilies' => $this->enableSmilies,
					'enableHtml' => $this->enableHtml,
					'enableBBCodes' => $this->enableBBCodes,
					'remembertime' => $this->remembertime
				),
				'attachmentHandler' => $this->attachmentHandler
			);
		}
		
		if ($this->canEditStatus()) {
			$todoData['data']['status'] = $this->status;
		}
		
		$this->objectAction = new ToDoAction(array($this->todo), 'update', $todoData);
		$this->objectAction->executeAction();
		
		if ($this->canEditResponsible()) {
			$this->updateResponsibles($this->todo->id, $this->responsibles, $this->todo->getResponsibleIDs());
		}
		
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
		
		if (!$this->todo->canEdit() && !$this->canEditStatus() && !$this->canEditResponsible())
			throw new PermissionDeniedException();
		
		$this->title = $this->todo->title;
		$this->description = $this->todo->description;
		$this->note = $this->todo->note;
		$this->status = $this->todo->status;
		$this->category = $this->todo->category;
		$this->enableSmilies = $this->todo->enableSmilies;
		$this->enableHtml = $this->todo->enableHtml;
		$this->enableBBCodes = $this->todo->enableBBCodes;
		$this->progress = $this->todo->progress;
		$this->important = $this->todo->important;
		$this->private = $this->todo->private;
		$this->canEditStatus = $this->canEditStatus();
		$this->canEditResponsible = $this->canEditResponsible();
		
		if ($this->todo->endTime > 0) {
			$this->endTime = DateUtil::getDateTimeByTimestamp($this->todo->endTime);
			$this->endTime->setTimezone(WCF::getUser()->getTimeZone());
			$this->endTime = $this->endTime->format('c');
		}
		
		if ($this->todo->remembertime > 0) {
			$this->remembertime = DateUtil::getDateTimeByTimestamp($this->todo->remembertime);
			$this->remembertime->setTimezone(WCF::getUser()->getTimeZone());
			$this->remembertime = $this->remembertime->format('Y-m-d');
		}
	}
	
	/**
	 *
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		if ($this->todo->categorytitle != '') {
			WCF::getBreadcrumbs()->add(new Breadcrumb($this->todo->categorytitle, LinkHandler::getInstance()->getLink('ToDoCategory', array(
				'id' => $this->todo->category
			))));
		}
		WCF::getBreadcrumbs()->add(new Breadcrumb($this->title, LinkHandler::getInstance()->getLink('ToDo', array(
			'id' => $this->todoID 
		))));
		
		WCF::getTPL()->assign( array(
			'id' => $this->todoID,
			'todo' => $this->todo,
			'action' => 'edit' 
		));
	}
	
	public function canEditStatus() {
		return $this->todo->canEditStatus();
	}
	
	public function canEditResponsible() {
		return $this->todo->canEditResponsible();
	}
}
