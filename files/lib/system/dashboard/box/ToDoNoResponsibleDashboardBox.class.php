<?php

namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\todo\DashboardBoxTodoList;
use wcf\data\todo\ToDo;
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
		
		$accessibleTodoIDs = ToDo::getAccessibleTodoIDs();
		
		if (!empty($accessibleTodoIDs)) {
			$todoList = new DashboardBoxTodoList(TODO_OUTSTANDING_DASHBOARD_BOX_ITEMS);
			$todoList->getConditionBuilder()->add('todo_table.statusID <> ?', array(1));
			$todoList->getConditionBuilder()->add('todo_table.todoID IN (?)', array($accessibleTodoIDs));
			$todoList->getConditionBuilder()->add('todo_table.todoID NOT IN (SELECT todo_user.todoID FROM wcf' . WCF_N . '_todo_to_user todo_user)');
			$todoList->getConditionBuilder()->add('todo_table.todoID NOT IN (SELECT todo_group.todoID FROM wcf' . WCF_N . '_todo_to_group todo_group)');
			$todoList->readObjects();
			
			WCF::getTPL()->assign(array(
				'todoList' => $todoList->getObjects()
			));
		} else {
			WCF::getTPL()->assign(array(
				'todoList' => array()
			));
		}
	}
	
	protected function render() {
		return WCF::getTPL()->fetch('dashboardBoxToDoList', 'wcf');
	}
}
