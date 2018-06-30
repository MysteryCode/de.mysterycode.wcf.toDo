<?php

namespace wcf\system\moderation\queue\activation;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\data\todo\ToDoAction;
use wcf\system\moderation\queue\AbstractToDoModerationQueueHandler;
use wcf\system\WCF;

/**
 * Implements the activation moderation of todos
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoModerationQueueActivationHandler extends AbstractToDoModerationQueueHandler implements IModerationQueueActivationHandler {
	/**
	 * @inheritDoc
	 */
	protected $definitionName = 'com.woltlab.wcf.moderation.activation';
	
	/**
	 * @inheritDoc
	 */
	protected $objectType = 'de.mysterycode.wcf.toDo.toDo';
	
	/**
	 * @inheritDoc
	 */
	public function enableContent(ModerationQueue $queue) {
		if ($this->isValid($queue->objectID) && $this->getTodo($queue->objectID)->isDisabled) {
			$todoAction = new ToDoAction([$this->getTodo($queue->objectID)], 'enable');
			$todoAction->executeAction();
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function getDisabledContent(ViewableModerationQueue $queue) {
		$todo = $queue->getAffectedObject();
		
		WCF::getTPL()->assign(['todo' => $todo]);
		
		return WCF::getTPL()->fetch('moderationTodo', 'wcf');
		
// 		// return template
// 		return WCF::getTPL()->fetch('moderationTodo', 'wcf', array(
// 			'todo' => $todo
// 		));
	}
}
