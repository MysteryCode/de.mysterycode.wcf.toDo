<?php

namespace wcf\system\cache\builder;


/**
 * 
 * 
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
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
