<?php

namespace wcf\system\moderation\queue\report;
use wcf\data\todo\ToDo;
use wcf\system\moderation\queue\AbstractToDoModerationQueueHandler;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\report\IModerationQueueReportHandler;
use wcf\system\WCF;

/**
 * Implements the report moderation of todos
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoModerationQueueReportHandler extends AbstractToDoModerationQueueHandler implements IModerationQueueReportHandler {
	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$definitionName
	 */
	protected $definitionName = 'com.woltlab.wcf.moderation.report';
	
	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$objectType
	 */
	protected $objectType = 'de.mysterycode.wcf.toDo.toDo';
	
	/**
	 * @see	\wcf\system\moderation\queue\report\IModerationQueueReportHandler::canReport()
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
	 * @see	\wcf\system\moderation\queue\report\IModerationQueueReportHandler::getReportedContent()
	 */
	public function getReportedContent(ViewableModerationQueue $queue) {
		WCF::getTPL()->assign(array(
			'todo' => new ToDo($queue->getAffectedObject())
		));
		
		return WCF::getTPL()->fetch('moderationTodo', 'wcf');
	}
	
	/**
	 * @see	\wcf\system\moderation\queue\report\IModerationQueueReportHandler::getReportedObject()
	 */
	public function getReportedObject($objectID) {
		if ($this->isValid($objectID)) {
			return $this->getTodo($objectID);
		}
		
		return null;
	}
}