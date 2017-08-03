<?php

namespace wcf\system\cache\builder;


/**
 * 
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryDataCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected $maxLifetime = 300;

	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = [
			'statObjects' => [],
			'lastTodoIDs' => []
		];
		
		//TODO
		
		return $data;
	}
}
