<?php

namespace wcf\system\event\listener;

/**
 * Exports the ip-addresses stored by the todo-plugin
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoExportGdprActionListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		/** @var \wcf\acp\action\UserExportGdprAction $eventObj */
		
		$eventObj->data['de.mysterycode.wcf.toDo'] = [
			'ipAddresses' => $eventObj->exportIpAddresses('wcf'.WCF_N.'_todo', 'ipAddress', 'time', 'submitter')
		];
	}
}
