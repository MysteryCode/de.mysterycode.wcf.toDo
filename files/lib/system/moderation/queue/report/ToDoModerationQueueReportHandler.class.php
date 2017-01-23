<?php

namespace wcf\system\moderation\queue\report;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\AbstractToDoModerationQueueHandler;
use wcf\system\WCF;

/**
 * Implements the report moderation of todos
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoModerationQueueReportHandler extends AbstractToDoModerationQueueHandler implements IModerationQueueReportHandler {
	/**
	 * @inheritDoc
	 */
	protected $definitionName = 'com.woltlab.wcf.moderation.report';
	
	/**
	 * @inheritDoc
	 */
	protected $objectType = 'de.mysterycode.wcf.toDo.toDo';
	
	/**
	 * @inheritDoc
	 */
	public function canReport($objectID) {
		if (!$this->isValid($objectID)) {
			return false;
		}
		
		if (!$this->getTodo($objectID)->canEnter()) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getReportedContent(ViewableModerationQueue $queue) {
		WCF::getTPL()->assign([
			'todo' => $queue->getAffectedObject()
		]);
		
		return WCF::getTPL()->fetch('moderationTodo', 'wcf');
	}
	
	/**
	 * @inheritDoc
	 */
	public function getReportedObject($objectID) {
		if ($this->isValid($objectID)) {
			return $this->getTodo($objectID);
		}
		
		return null;
	}
}
