<?php

namespace wcf\system\cache\builder;
use wcf\data\object\type\ObjectTypeCache;

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * 
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoCategoryLabelGroupCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		// get object type
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.label.objectType', 'de.mysterycode.wcf.toDo.category');
		if ($objectType === null) {
			return array();
		}
		
		// prepare conditions
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('objectTypeID = ?', array($objectType->objectTypeID));
		
		// read label group associations
		$data = array();
		$sql = "SELECT	groupID, objectID
			FROM	wcf".WCF_N."_label_group_to_object
			".$conditionBuilder;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		while ($row = $statement->fetchArray()) {
			if (!isset($data[$row['objectID']])) {
				$data[$row['objectID']] = array();
			}
			
			$data[$row['objectID']][] = $row['groupID'];
		}
		
		return $data;
	}
}
