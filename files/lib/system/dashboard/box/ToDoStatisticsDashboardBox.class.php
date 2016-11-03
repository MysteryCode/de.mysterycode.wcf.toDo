<?php

namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows the todo statistics dashboardbox.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoStatisticsDashboardBox extends AbstractSidebarDashboardBox {
	public $todoStat = array();
	
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
		
		if (WCF::getSession()->getPermission('user.toDo.toDo.canView')) {
			// read stats some time
		}
		WCF::getTPL()->assign(array(
				'todoStat' => $this->todoStat
		));
	}
	
	protected function render() {
		return WCF::getTPL()->fetch('dashboardBoxToDoStatistics', 'wcf');
	}
}
