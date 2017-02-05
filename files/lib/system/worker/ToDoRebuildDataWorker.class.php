<?php

namespace wcf\system\worker;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\todo\ToDoEditor;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\user\activity\point\UserActivityPointHandler;
use wcf\system\WCF;

/**
 * Implements the todo rebuild data worker
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoRebuildDataWorker extends AbstractRebuildDataWorker {
	/**
	 * @see	\wcf\system\worker\AbstractRebuildDataWorker::$objectListClassName
	 */
	protected $objectListClassName = 'wcf\data\todo\ToDoList';
	
	/**
	 * @see	\wcf\system\worker\AbstractWorker::$limit
	 */
	protected $limit = 400;

	/**
	 * @see	\wcf\system\worker\AbstractRebuildDataWorker::initObjectList
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		$this->objectList->sqlOrderBy = 'todo_table.todoID';
	}

	/**
	 * @see	\wcf\system\worker\IWorker::execute()
	 */
	public function execute() {
		parent::execute();
		
		if (!$this->loopCount) {
			// reset activity points
			UserActivityPointHandler::getInstance()->reset('de.mysterycode.wcf.toDo.toDo.activityPointEvent');
		}
		
		if (!count($this->objectList)) {
			return;
		}
		
		// fetch cumulative likes
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("objectTypeID = ?", array(
			ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.like.likeableObject', 'de.mysterycode.wcf.toDo.toDo')
		));
		$conditions->add("objectID IN (?)", array(
			$this->objectList->getObjectIDs()
		));
		
		$sql = "SELECT	objectID, cumulativeLikes
			FROM	wcf" . WCF_N . "_like_object
			" . $conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		$cumulativeLikes = array();
		while ($row = $statement->fetchArray()) {
			$cumulativeLikes[$row['objectID']] = $row['cumulativeLikes'];
		}
		
		$userStats = array();
		WCF::getDB()->beginTransaction();
		/** @var \wcf\data\todo\ToDo $todo */
		foreach ($this->objectList as $todo) {
			$data = array();
			
			// update activity points
			if ($todo->submitter) {
				if (!isset($userStats[$todo->submitter])) {
					$userStats[$todo->submitter] = 0;
				}
				$userStats[$todo->submitter]++;
			}
			
			if (!empty($todo->description)) {
				if ($todo->hasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects('de.mysterycode.wcf.toDo', $todo->todoID, $todo->description)) {
					$data['hasEmbeddedObjects'] = $todo->hasEmbeddedObjects ? 0 : 1;
				}
			}
			
			if (!empty($data)) {
				$todoEditor = new ToDoEditor($todo);
				$todoEditor->update($data);
			}
		}
		WCF::getDB()->commitTransaction();
		
		// update activity points
		UserActivityPointHandler::getInstance()->fireEvents('de.mysterycode.wcf.toDo.toDo.activityPointEvent', $userStats, false);
	}
}
