<?php

namespace wcf\system\event\listener;

/**
 * Removes the ip-addresses stored by the todo-plugin
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoIpAddressRemovalGdprActionListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		/** @var \wcf\system\cronjob\RemoveIpAddressesCronjob $eventObj */
		
		$eventObj->dbTableData['de.mysterycode.wcf.toDo'] = ['wcf' . WCF_N . '_todo'];
	}
}
