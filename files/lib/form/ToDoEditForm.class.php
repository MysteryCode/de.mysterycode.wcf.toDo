<?php

namespace wcf\form;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoAction;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;

/**
 * Shows the toDoEdit form.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
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
		MessageForm::readParameters();
		
		MessageQuoteManager::getInstance()->readParameters();
		
		if (isset($_REQUEST['id'])) $this->todoID = intval($_REQUEST['id']);
		$this->todo = new ToDo($this->todoID);
		if (!$this->todo->todoID)
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
					'updatetimestamp' => TIME_NOW,
					'endTime' => $this->endTime,
					'private' => $this->private,
					'important' => $this->important,
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
			$todoData['data']['statusID'] = $this->statusID;
		}
		
		$this->objectAction = new ToDoAction(array($this->todo), 'update', $todoData);
		$this->objectAction->executeAction();
		
		if ($this->todo->canEditResponsibles()) {
			$responsibleUserAction = new ToDoAction(array($this->todo->todoID), 'updateResponsibles', array('search' => $this->responsibles));
			$responsibleUserAction->executeAction();
			
			$responsibleGroupAction = new ToDoAction(array($this->todo->todoID), 'updateResponsibleGroups', array('search' => $this->responsibleGroups));
			$responsibleGroupAction->executeAction();
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
		
		$this->category = $this->todo->getCategory();
		$this->categoryID = $this->category->categoryID;
		
		if (empty($_POST)) {
			$this->responsibles = $this->todo->getFormattedResponsibles();
			$this->responsibleGroups = $this->todo->getFormattedResponsibleGroups();
			
			if (!$this->todo->canEdit())
				throw new PermissionDeniedException();
			
			$this->title = $this->todo->title;
			$this->description = $this->todo->description;
			$this->note = $this->todo->note;
			$this->statusID = $this->todo->statusID;
			$this->categoryID = $this->todo->categoryID;
			$this->category = $this->todo->getCategory();
			$this->enableSmilies = $this->todo->enableSmilies;
			$this->enableHtml = $this->todo->enableHtml;
			$this->enableBBCodes = $this->todo->enableBBCodes;
			$this->progress = $this->todo->progress;
			$this->important = $this->todo->important;
			$this->private = $this->todo->private;
			$this->canEditStatus = $this->canEditStatus();
			$this->canEditResponsible = $this->canEditResponsible();
			
			if ($this->todo->endTime > 0)
				$this->endTime = DateUtil::getDateTimeByTimestamp($this->todo->endTime);
			
			if ($this->todo->remembertime > 0)
				$this->remembertime = DateUtil::getDateTimeByTimestamp($this->todo->remembertime);
		} else {
			$this->endTime = DateUtil::getDateTimeByTimestamp($this->endTime);
			$this->remembertime = DateUtil::getDateTimeByTimestamp($this->remembertime);
		}
		
		if (!empty($this->endTime)) {
			$this->endTime->setTimezone(WCF::getUser()->getTimeZone());
			$this->endTime = $this->endTime->format('c');
		}
		if (!empty($this->remembertime)) {
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
