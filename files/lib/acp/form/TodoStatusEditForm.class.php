<?php

namespace wcf\acp\form;
use wcf\data\todo\status\TodoStatusCache;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\data\todo\status\TodoStatus;
use wcf\data\todo\status\TodoStatusAction;

/**
 * Shows the field edit form.
 *
 * @author	Florian Gail
 * @copyright	2014-2015 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @package	de.mysterycode.wcf.inventar
 * @category	INVENTAR
 */
class TodoStatusEditForm extends TodoStatusAddForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.todoStatus';
	
	/**
	 * status id
	 * @var	integer
	 */
	public $statusID = 0;
	
	/**
	 * status object
	 * @var	\inventar\data\field\Field
	 */
	public $status = null;
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->statusID = intval($_REQUEST['id']);
		$this->status = new TodoStatus($this->statusID);
		if (!$this->status->statusID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();

		$this->subject = 'wcf.acp.todo.status.status'.$this->status->fieldID;
		if (I18nHandler::getInstance()->isPlainValue('subject')) {
			I18nHandler::getInstance()->remove($this->subject);
			$this->subject = I18nHandler::getInstance()->getValue('subject');
		} else {
			I18nHandler::getInstance()->save('subject', $this->subject, 'wcf.acp.todo', 1);
		}
		
		$this->description = 'wcf.acp.todo.status.description'.$this->status->fieldID;
		if (I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->remove($this->description);
			$this->description = I18nHandler::getInstance()->getValue('description');
		} else {
			I18nHandler::getInstance()->save('description', $this->description, 'wcf.acp.todo', 1);
		}
		
		// update status
		$this->objectAction = new TodoStatusAction(array($this->statusID), 'update', array(
			'data' => array(
				'subject' => $this->subject,
				'description' => $this->description,
				'cssClass' => $this->cssClass,
				'showOrder' => $this->showOrder
			)
		));
		$this->objectAction->executeAction();
		$this->saved();
		
		// show success
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (empty($_POST)) {
			I18nHandler::getInstance()->setOptions('subject', 1, $this->status->subject, 'wcf.acp.todo.status\d+');
			I18nHandler::getInstance()->setOptions('description', 1, $this->status->description, 'wcf.acp.todo.status\d+');
			$this->showOrder = $this->status->showOrder;
			$this->cssClass = $this->status->cssClass;
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables(!empty($_POST));
		
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'statusID' => $this->statusID,
			'status' => $this->status,
			'availableStatus' => TodoStatusCache::getInstance()->getStatusList()
		));
	}
}
