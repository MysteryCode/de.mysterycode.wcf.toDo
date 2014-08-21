<?php
namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\todo\ToDoList;
use wcf\data\user\User;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows the todo outstanding toDos dashboardbox.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoOutstandingDashboardBox extends AbstractSidebarDashboardBox {
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
		
		$todoList = new ToDoList();
		$todoList->getConditionBuilder()->add('(status = ? OR status = ?)', array(1, 2));
		$todoList->readObjects();
		
		//WCF::getUser()->userID
		
		WCF::getTPL()->assign(array(
			'todoList' => $todoList->getObjects()
		));
	}
	
	protected function render() {
		return WCF::getTPL()->fetch('dashboardBoxToDoList', 'wcf');
	}
}