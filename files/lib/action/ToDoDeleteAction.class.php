<?php

namespace wcf\action;
use wcf\data\todo\ToDoAction;
use wcf\system\request\LinkHandler;
use wcf\util\HeaderUtil;

/**
 * Represents the toDoDelete action.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoDeleteAction extends AbstractAction {
	public $neededPermissions = array('user.toDo.toDo.canDelete');
	public $neededModules = array('TODOLIST');
	public $toDoID = 0;
	
	/**
	 *
	 * @see wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if(isset($_REQUEST['id'])) $this->todoID = intval($_REQUEST['id']);
	}
	
	public function execute() {
		$this->objectAction = new ToDoAction(array($this->todoID), 'delete');
		$this->objectAction->executeAction();
		
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('ToDoList', array()));
		exit();
	}
}