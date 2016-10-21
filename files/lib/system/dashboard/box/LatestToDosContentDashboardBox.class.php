<?php
namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\todo\DashboardBoxTodoList;
use wcf\page\IPage;
use wcf\system\WCF;

/**
 * Shows the latest todos.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class LatestToDosContentDashboardBox extends AbstractContentDashboardBox {
	/**
	 * todo list
	 * @var	\wcf\data\todo\ToDoList
	 */
	public $latestTodosList = null;
	
	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		$this->latestTodosList = new DashboardBoxTodoList(TODO_LATEST_DASHBOARD_BOX_ITEMS);
		$this->latestTodosList->sqlOrderBy = 'time DESC';
		$this->latestTodosList->readObjects();
		
		$this->fetched();
	}
	
	/**
	 * @see	\wcf\system\dashboard\box\AbstractContentDashboardBox::render()
	 */
	protected function render() {
		if (!count($this->latestTodosList))
			return '';
		
		WCF::getTPL()->assign(array(
			'latestTodosList' => $this->latestTodosList
		));
		
		return WCF::getTPL()->fetch('dashboardBoxLatestToDos', 'wcf');
	}
}
