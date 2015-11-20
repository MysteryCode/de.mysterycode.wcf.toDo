<?php

namespace wcf\system\cache\builder;
use wcf\system\cache\builder\AbstractCacheBuilder;

class TodoCategoryDataCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::$maxLifetime
	 */
	protected $maxLifetime = 300;

	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$data = array('statObjects' => array(), 'lastTodoIDs' => array());
		
		//TODO
		
		return $data;
	}
}
