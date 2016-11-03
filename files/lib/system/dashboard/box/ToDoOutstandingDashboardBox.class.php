<?php

namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\todo\DashboardBoxTodoList;
use wcf\data\todo\ToDo;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows the todo outstanding toDos dashboardbox.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoOutstandingDashboardBox extends AbstractSidebarDashboardBox {
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
		
		$accessibleTodoIDs = ToDo::getAccessibleTodoIDs();
		
		if (!empty($accessibleTodoIDs)) {
			$todoList = new DashboardBoxTodoList(TODO_OUTSTANDING_DASHBOARD_BOX_ITEMS);
			$todoList->getConditionBuilder()->add('todo_table.statusID <> ?', array(1));
			$todoList->getConditionBuilder()->add('todo_table.todoID IN (?)', array($accessibleTodoIDs));
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
