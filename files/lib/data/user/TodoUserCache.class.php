<?php

namespace wcf\data\user;
use wcf\system\cache\builder\TodoUserCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * 
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoUserCache extends SingletonFactory {
	/**
	 * cached users
	 */
	protected $users = [];
	
	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->users = TodoUserCacheBuilder::getInstance()->getData([], 'users');
	}
	
	public function getUser($userID) {
		if (!empty($this->users[$userID]))
			return $this->users[$userID];
		
		return null;
	}
	
	public function getUsers() {
		return $this->users;
	}
}
