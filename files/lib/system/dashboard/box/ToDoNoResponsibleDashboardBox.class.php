<?php

namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\todo\DashboardBoxTodoList;
use wcf\data\user\User;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows the todo no responsible dashboardbox.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoNoResponsibleDashboardBox extends AbstractSidebarDashboardBox {
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		$todoList = new DashboardBoxTodoList(TODO_NORESPONSIBLE_DASHBOARD_BOX_ITEMS);
		$todoList->getConditionBuilder()->add('(private = ? OR submitter = ?)', array(0, WCF::getUser()->userID));
		$todoList->getConditionBuilder()->add('status != ?', array(4));
		$todoList->getConditionBuilder()->add('status != ?', array(3));
		$todoList->readObjects();
		
		if (!WCF::getSession()->getPermission('user.toDo.toDo.canViewList')) {
			WCF::getTPL()->assign(array(
				'todoList' => array()
			));
		} else {
			WCF::getTPL()->assign(array(
				'todoList' => $todoList->getObjects()
			));
		}
	}
	
	protected function render() {
		return WCF::getTPL()->fetch('dashboardBoxToDoList', 'wcf');
	}
}
