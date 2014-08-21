<?php
namespace wcf\system\moderation\queue\activation;
use wcf\data\todo\ToDoAction;
use wcf\data\todo\ToDo;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\activation\IModerationQueueActivationHandler;
use wcf\system\moderation\queue\AbstractToDoModerationQueueHandler;
use wcf\system\WCF;

/**
 * Implements the activation moderation of todos
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoModerationQueueActivationHandler extends AbstractToDoModerationQueueHandler implements IModerationQueueActivationHandler {
	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$definitionName
	 */
	protected $definitionName = 'com.woltlab.wcf.moderation.activation';
	
	/**
	 * @see	\wcf\system\moderation\queue\AbstractModerationQueueHandler::$objectType
	 */
	protected $objectType = 'de.mysterycode.wcf.toDo.toDo';
	
	/**
	 * @see	\wcf\system\moderation\queue\activation\IModerationQueueActivationHandler::enableContent()
	 */
	public function enableContent(ModerationQueue $queue) {
		if ($this->isValid($queue->objectID) && $this->getTodo($queue->objectID)->isDisabled) {
			$todoAction = new ToDoAction(array($this->getTodo($queue->objectID)), 'enable');
			$todoAction->executeAction();
		}
	}
	
	/**
	 * @see	\wcf\system\moderation\queue\activation\IModerationQueueActivationHandler::getDisabledContent()
	 */
	public function getDisabledContent(ViewableModerationQueue $queue) {
		$todo = $queue->getAffectedObject();
		
		WCF::getTPL()->assign(array('todo' => $todo));
		
		return WCF::getTPL()->fetch('moderationTodo', 'wcf');
		
// 		// return template
// 		return WCF::getTPL()->fetch('moderationTodo', 'wcf', array(
// 				'todo' => $todo
// 		));
	}
}
