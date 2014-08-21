<?php
namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\todo\ToDoList;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows Sort-Order options to sort the list
 *
 * @author	Daniel Nold
 * @copyright	2014 Daniel Nold
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoSortOrderDashboardBox extends AbstractSidebarDashboardBox {
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
	}
	
	protected function render() {
		return WCF::getTPL()->fetch('dashboardBoxToDoSortOrder', 'wcf');
	}
}