<?php

namespace wcf\data\todo\status;

use wcf\system\cache\builder\TodoStatusCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * 
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoStatusCache extends SingletonFactory {
	/**
	 * cached status
	 * @var	array<\wcf\data\todo\status\TodoStatus>
	 */
	protected $cachedStatus = [];
	
	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->cachedStatus = TodoStatusCacheBuilder::getInstance()->getData([], 'status');
	}
	
	/**
	 * Returns the status with the given status id id from cache.
	 * 
	 * @param	integer		$statusID
	 * @return	\wcf\data\todo\status\TodoStatus
	 */
	public function getStatus($statusID) {
		if (!isset($this->cachedStatus[$statusID])) {
			return null;
		}
		
		return $this->cachedStatus[$statusID];
	}
	
	public function getStatusList() {
		return $this->cachedStatus;
	}
	
	/**
	 * Returns a list of all contacts.
	 * 
	 * @return	array<\wcf\data\todo\status\TodoStatus>
	 */
	public function getContacts() {
		return $this->cachedStatus;
	}
}
