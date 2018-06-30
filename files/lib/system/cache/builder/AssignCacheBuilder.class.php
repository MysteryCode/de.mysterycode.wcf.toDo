<?php

namespace wcf\system\cache\builder;
use wcf\data\todo\assigned\group\AssignedGroupList;
use wcf\data\todo\assigned\user\AssignedUserList;

/**
 *
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class AssignCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = [
			'todos' => [],
			'users' => [],
			'groups' => []
		];
		
		// users
		$userList = new AssignedUserList();
		$userList->readObjects();
		$users = $userList->getObjects();
		
		// groups
		$groupList = new AssignedGroupList();
		$groupList->readObjects();
		$groups = $groupList->getObjects();

		/** @var \wcf\data\todo\assigned\user\AssignedUser $user */
		foreach ($users as $user) {
			$data['users'][$user->userID][] = $user;
			$data['todos'][$user->todoID]['users'][] = $user;
		}

		/** @var \wcf\data\todo\assigned\group\AssignedGroup $group */
		foreach ($groups as $group) {
			$data['groups'][$group->groupID][] = $group;
			$data['todos'][$group->todoID]['groups'][] = $group;
		}
		
		return $data;
	}
}
