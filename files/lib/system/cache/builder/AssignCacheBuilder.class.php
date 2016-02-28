<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\assigned\group\AssignedGroupList;
use wcf\data\todo\assigned\user\AssignedUserList;
use wcf\data\todo\ToDoList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 *
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @contact	de.mysterycode.inventar
 * @category 	inventar
 */
class AssignCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
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
