<?php

namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\todo\status\TodoStatusAction;
use wcf\data\todo\status\TodoStatusEditor;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the field add form.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoStatusAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.todoStatus.add';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.content.toDo.status.canAdd');

	/**
	 * subject
	 * @var	string
	 */
	public $subject = '';
	
	/**
	 * description
	 * @var	string
	 */
	public $description = '';
	
	/**
	 * show order
	 * @var	integer
	 */
	public $showOrder = 0;
	
	/**
	 * css classname
	 * @var	string
	 */
	public $cssClass = '';
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		I18nHandler::getInstance()->register('subject');
		I18nHandler::getInstance()->register('description');
	}
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();

		if (I18nHandler::getInstance()->isPlainValue('subject')) $this->subject = I18nHandler::getInstance()->getValue('subject');
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = I18nHandler::getInstance()->getValue('description');
		if (isset($_POST['cssClass'])) $this->cssClass = StringUtil::trim($_POST['cssClass']);
		if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// subject
		if (!I18nHandler::getInstance()->validateValue('subject')) {
			if (I18nHandler::getInstance()->isPlainValue('subject')) {
				throw new UserInputException('subject');
			}
			else {
				throw new UserInputException('subject', 'multilingual');
			}
		}
		
		// description
		if (!I18nHandler::getInstance()->validateValue('description')) {
			if (I18nHandler::getInstance()->isPlainValue('description')) {
				throw new UserInputException('description');
			}
			else {
				throw new UserInputException('description', 'multilingual');
			}
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		// save field
		$this->objectAction = new TodoStatusAction(array(), 'create', array(
			'data' => array(
				'subject' => $this->subject,
				'description' => $this->description,
				'showOrder' => $this->showOrder,
				'cssClass' => $this->cssClass
			)
		));
		$this->objectAction->executeAction();
		
		if (!I18nHandler::getInstance()->isPlainValue('subject')) {
			$returnValues = $this->objectAction->getReturnValues();
			$statusID = $returnValues['returnValues']->statusID;
			I18nHandler::getInstance()->save('subject', 'wcf.acp.todo.status.status'.$statusID, 'wcf.acp.todo', PackageCache::getInstance()->getPackageID('de.mysterycode.wcf.toDo'));
			
			// update subject
			$statusEditor = new TodoStatusEditor($returnValues['returnValues']);
			$statusEditor->update(array(
				'subject' => 'wcf.acp.todo.status.status'.$statusID
			));
		}
		
		if (!I18nHandler::getInstance()->isPlainValue('description')) {
			$returnValues = $this->objectAction->getReturnValues();
			$statusID = $returnValues['returnValues']->statusID;
			I18nHandler::getInstance()->save('description', 'wcf.acp.todo.status.description'.$statusID, 'wcf.acp.todo', PackageCache::getInstance()->getPackageID('de.mysterycode.wcf.toDo'));
			
			// update description
			$statusEditor = new TodoStatusEditor($returnValues['returnValues']);
			$statusEditor->update(array(
				'description' => 'wcf.acp.todo.status.description'.$statusID
			));
		}
		$this->saved();
		
		// reset values
		$this->subject = $this->description = $this->cssClass = '';
		$this->showOrder = 0;
		
		I18nHandler::getInstance()->reset();
		
		// show success
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'subject' => $this->subject,
			'description' => $this->description,
			'showOrder' => $this->showOrder,
			'cssClass' => $this->cssClass
		));
	}
}
