<?php

namespace wcf\data\user;
use wcf\system\cache\builder\TodoUserCacheBuilder;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * 
 * 
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @contact	de.mysterycode.inventar
 * @category 	MCPS
 */
class TodoUserCache extends SingletonFactory {
	/**
	 * cached users
	 */
	protected $users = array();
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->users = TodoUserCacheBuilder::getInstance()->getData(array(), 'users');
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
