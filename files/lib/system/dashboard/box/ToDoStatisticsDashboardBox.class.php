<?php
namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows the todo statistics dashboardbox.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoStatisticsDashboardBox extends AbstractSidebarDashboardBox {
	public $todoStat = array();
	
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);
		
		$sql = "SELECT COUNT(id)
			FROM wcf" . WCF_N . "_todo";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$stat = $statement->fetchArray();
		$this->todoStat[0] = array(
			'type' => 'all',
			'count' => $stat['COUNT(id)']
		);
		
		$sql = "SELECT COUNT(id)
			FROM wcf" . WCF_N . "_todo
			WHERE wcf" . WCF_N . "_todo.status = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(1));
		$stat = $statement->fetchArray();
		$this->todoStat[1] = array(
			'type' => 'unsolved',
			'count' => $stat['COUNT(id)']
		);
		
		$sql = "SELECT COUNT(id)
			FROM wcf" . WCF_N . "_todo
			WHERE wcf" . WCF_N . "_todo.status = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(2));
		$stat = $statement->fetchArray();
		$this->todoStat[2] = array(
			'type' => 'work',
			'count' => $stat['COUNT(id)']
		);
		
		$sql = "SELECT COUNT(id)
			FROM wcf" . WCF_N . "_todo
			WHERE wcf" . WCF_N . "_todo.status = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(3));
		$stat = $statement->fetchArray();
		$this->todoStat[3] = array(
			'type' => 'solved',
			'count' => $stat['COUNT(id)']
		);
		
		$sql = "SELECT COUNT(id)
			FROM wcf" . WCF_N . "_todo
			WHERE wcf" . WCF_N . "_todo.status = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(4));
		$stat = $statement->fetchArray();
		$this->todoStat[4] = array(
			'type' => 'canceled',
			'count' => $stat['COUNT(id)']
		);
		
		WCF::getTPL()->assign(array(
			'todoStat' => $this->todoStat
		));
	}
	
	protected function render() {
		return WCF::getTPL()->fetch('dashboardBoxToDoStatistics', 'wcf');
	}
}