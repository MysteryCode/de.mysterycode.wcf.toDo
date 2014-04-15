<?php

namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\user\User;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows the todo no responsible dashboardbox.
 *
 * @author Florian Gail
 * @copyright 2013 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoNoResponsibleDashboardBox extends AbstractSidebarDashboardBox {
	public $templateName = 'dashboardBoxToDoList';
	
	public $toDoList = array ();
	public function init(DashboardBox $box, IPage $page) {
		parent::init ( $box, $page );
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo
			WHERE ( private = ? OR submitter = ? )
				AND status != ?
				AND status != ?
			ORDER BY endTime DESC
			LIMIT ?";
		$statement = WCF::getDB ()->prepareStatement ( $sql );
		$statement->execute ( array (
			0,
			WCF::getUser ()->userID,
			4,
			3,
			5 
		) );
		
		while ( $stat = $statement->fetchArray () ) {
			
			$sqlCheck = "SELECT toDoID
				FROM wcf" . WCF_N . "_todo_to_user
				WHERE toDoID = ?";
			$check = WCF::getDB ()->prepareStatement ( $sqlCheck );
			$check->execute ( array (
				$stat ['id'] 
			) );
			
			if (! $check->fetchArray ()) {
				$user = new User ( $stat ['submitter'] );
				$this->toDoList [] = array (
					'id' => $stat ['id'],
					'title' => $stat ['title'],
					'submitter' => $stat ['submitter'],
					'username' => $user->username,
					'timestamp' => $stat ['timestamp'] 
				);
			}
		}
		
		WCF::getTPL ()->assign ( array (
			'toDoList' => $this->toDoList 
		) );
	}
	protected function render() {
		return WCF::getTPL ()->fetch ( 'dashboardBoxToDoNoResponsible', 'wcf' );
	}
}