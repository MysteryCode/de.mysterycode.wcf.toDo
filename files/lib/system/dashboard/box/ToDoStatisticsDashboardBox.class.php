<?php

namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows the todo statistics dashboardbox.
 *
 * @author Florian Gail
 * @copyright 2013 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoStatisticsDashboardBox extends AbstractSidebarDashboardBox {
	public $toDoList = array ();
	public function init(DashboardBox $box, IPage $page) {
		parent::init ( $box, $page );
		
		$sql = "SELECT COUNT(*)
			FROM wcf" . WCF_N . "_todo";
		$statement = WCF::getDB ()->prepareStatement ( $sql );
		$statement->execute ();
		$stat = $statement->fetchArray ();
		$this->toDoList [0] = array (
				'type' => 'all',
				'count' => $stat ['COUNT(*)'] 
		);
		
		$sql = "SELECT COUNT(*)
			FROM wcf" . WCF_N . "_todo
			WHERE wcf" . WCF_N . "_todo.status = ?";
		$statement = WCF::getDB ()->prepareStatement ( $sql );
		$statement->execute ( array (
				1 
		) );
		$stat = $statement->fetchArray ();
		$this->toDoList [1] = array (
				'type' => 'unsolved',
				'count' => $stat ['COUNT(*)'] 
		);
		
		$sql = "SELECT COUNT(*)
			FROM wcf" . WCF_N . "_todo
			WHERE wcf" . WCF_N . "_todo.status = ?";
		$statement = WCF::getDB ()->prepareStatement ( $sql );
		$statement->execute ( array (
				2 
		) );
		$stat = $statement->fetchArray ();
		$this->toDoList [2] = array (
				'type' => 'work',
				'count' => $stat ['COUNT(*)'] 
		);
		
		$sql = "SELECT COUNT(*)
			FROM wcf" . WCF_N . "_todo
			WHERE wcf" . WCF_N . "_todo.status = ?";
		$statement = WCF::getDB ()->prepareStatement ( $sql );
		$statement->execute ( array (
				3 
		) );
		$stat = $statement->fetchArray ();
		$this->toDoList [3] = array (
				'type' => 'solved',
				'count' => $stat ['COUNT(*)'] 
		);
		
		$sql = "SELECT COUNT(*)
			FROM wcf" . WCF_N . "_todo
			WHERE wcf" . WCF_N . "_todo.status = ?";
		$statement = WCF::getDB ()->prepareStatement ( $sql );
		$statement->execute ( array (
				4 
		) );
		$stat = $statement->fetchArray ();
		$this->toDoList [4] = array (
				'type' => 'canceled',
				'count' => $stat ['COUNT(*)'] 
		);
		
		WCF::getTPL ()->assign ( array (
				'toDoList' => $this->toDoList 
		) );
	}
	protected function render() {
		return WCF::getTPL ()->fetch ( 'dashboardBoxToDoStatistics', 'wcf' );
	}
}