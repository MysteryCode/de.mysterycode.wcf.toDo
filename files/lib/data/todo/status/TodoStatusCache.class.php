<?php

namespace wcf\data\todo\status;
use wcf\data\todo\status\TodoStatus;
use wcf\system\cache\builder\TodoStatusCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * 
 * 
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @contact	de.mysterycode.inventar
 * @category 	MCPS
 */
class TodoStatusCache extends SingletonFactory {
	/**
	 * cached status
	 * @var	array<\wcf\data\todo\status\TodoStatus>
	 */
	protected $cachedStatus = array();
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->cachedStatus = TodoStatusCacheBuilder::getInstance()->getData(array(), 'status');
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
