<?php

namespace wcf\system\dashboard\box;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\user\User;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

/**
 * Shows the todo outstanding toDos dashboardbox.
 *
 * @author Florian Gail
 * @copyright 2013 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoOutstandingDashboardBox extends AbstractSidebarDashboardBox {
	public $templateName = 'dashboardBoxToDoList';
	
	public $toDoList = array ();
	public function init(DashboardBox $box, IPage $page) {
		parent::init ( $box, $page );
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo,wcf" . WCF_N . "_todo_to_user
			WHERE wcf" . WCF_N . "_todo.id = wcf" . WCF_N . "_todo_to_user.toDoID
				AND (wcf" . WCF_N . "_todo.status = ? OR wcf" . WCF_N . "_todo.status = ?)
				AND wcf" . WCF_N . "_todo_to_user.userID = ?
			LIMIT ?";
		$statement = WCF::getDB ()->prepareStatement ( $sql );
		$statement->execute ( array (
				1,
				2,
				WCF::getUser ()->userID,
				5 
		) );
		
		while ( $stat = $statement->fetchArray () ) {
			$user = new User ( $stat ['submitter'] );
			$this->toDoList [] = array (
					'id' => $stat ['id'],
					'title' => $stat ['title'],
					'submitter' => $stat ['submitter'],
					'username' => $user->username,
					'timestamp' => $stat ['timestamp'] 
			);
		}
		
		WCF::getTPL ()->assign ( array (
				'toDoList' => $this->toDoList 
		) );
	}
	protected function render() {
		return WCF::getTPL ()->fetch ( 'dashboardBoxToDoOutstanding', 'wcf' );
	}
}