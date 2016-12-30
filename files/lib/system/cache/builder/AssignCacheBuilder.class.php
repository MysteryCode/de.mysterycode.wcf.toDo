<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\assigned\group\AssignedGroupList;
use wcf\data\todo\assigned\user\AssignedUserList;
use wcf\data\todo\ToDoList;

/**
 *
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = array(
			'todos' => array(),
			'users' => array(),
			'groups' => array()
		);
		
		// todos
		$todoList = new ToDoList();
		$todoList->readObjects();
		$todos = $todoList->readObjects();
		
		// users
		$userList = new AssignedUserList();
		$userList->readObjects();
		$users = $userList->getObjects();
		
		// groups
		$groupList = new AssignedGroupList();
		$groupList->readObjects();
		$groups = $groupList->getObjects();
		
		foreach ($users as $user) {
			$data['users'][$user->userID][] = $user;
			$data['todos'][$user->todoID]['users'][] = $user;
		}
		
		foreach ($groups as $group) {
			$data['groups'][$group->groupID][] = $group;
			$data['todos'][$group->todoID]['groups'][] = $group;
		}
		
		return $data;
	}
}
