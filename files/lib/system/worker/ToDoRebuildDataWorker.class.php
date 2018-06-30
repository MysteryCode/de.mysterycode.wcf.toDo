<?php

namespace wcf\system\worker;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\todo\ToDoEditor;
use wcf\data\todo\ToDoList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\user\activity\event\UserActivityEventHandler;
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
	 * @inheritDoc
	 */
	protected $objectListClassName = ToDoList::class;
	
	/**
	 * @inheritDoc
	 */
	protected $limit = 400;

	/**
	 * @var HtmlInputProcessor
	 */
	protected $htmlInputProcessor;

	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		$this->objectList->sqlOrderBy = 'todo_table.todoID';
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		$this->objectList->getConditionBuilder()->add('todo_table.todoID BETWEEN ? AND ?', [$this->limit * $this->loopCount + 1, $this->limit * $this->loopCount + $this->limit]);

		parent::execute();
		
		if (!$this->loopCount) {
			// reset activity points
			UserActivityPointHandler::getInstance()->reset('de.mysterycode.wcf.toDo.toDo.activityPointEvent');
		}
		
		if ($this->objectList === null || !count($this->objectList)) {
			return;
		}
		
		// fetch cumulative likes
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("objectTypeID = ?", [
			ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.like.likeableObject', 'de.mysterycode.wcf.toDo.toDo.like')
		]);
		$conditions->add("objectID IN (?)", [
			$this->objectList->getObjectIDs()
		]);

		// prepare statements
		$attachmentObjectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.attachment.objectType', 'de.mysterycode.wcf.toDo.toDo');
		$sql = "SELECT		COUNT(*) AS attachments
			FROM		wcf".WCF_N."_attachment
			WHERE		objectTypeID = ?
					AND objectID = ?";
		$attachmentStatement = WCF::getDB()->prepareStatement($sql);
		
		$sql = "SELECT	objectID, cumulativeLikes
			FROM	wcf" . WCF_N . "_like_object
			" . $conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		$cumulativeLikes = [];
		while ($row = $statement->fetchArray()) {
			$cumulativeLikes[$row['objectID']] = $row['cumulativeLikes'];
		}
		
		UserActivityEventHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.recentActivityEvent', $this->objectList->getObjectIDs());
		UserActivityEventHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.like.recentActivityEvent', $this->objectList->getObjectIDs());
		UserActivityPointHandler::getInstance()->removeEvents('de.mysterycode.wcf.toDo.toDo.activityPointEvent', $this->objectList->getObjectIDs());
		
		$userStats = [];
		WCF::getDB()->beginTransaction();
		/** @var \wcf\data\todo\ToDo $todo */
		foreach ($this->objectList as $todo) {
			// editor
			$editor = new ToDoEditor($todo);

			// update activity points
			if ($todo->submitter) {
				if (!isset($userStats[$todo->submitter])) {
					$userStats[$todo->submitter] = 0;
				}
				$userStats[$todo->submitter]++;
			}

			// count attachments
			$attachmentStatement->execute([$attachmentObjectType->objectTypeID, $todo->todoID]);
			$row = $attachmentStatement->fetchSingleRow();
			$data['attachments'] = $row['attachments'];

			// update cumulative likes
			$data['cumulativeLikes'] = isset($cumulativeLikes[$todo->todoID]) ? $cumulativeLikes[$todo->todoID] : 0;

			// update description
			if (!$todo->enableHtml) {
				$this->getHtmlInputProcessor()->process($todo->description, 'de.mysterycode.wcf.toDo', $todo->todoID, true);
				$data['description'] = $this->getHtmlInputProcessor()->getHtml();
				$data['enableHtml'] = 1;
			} else {
				$this->getHtmlInputProcessor()->processEmbeddedContent($todo->description, 'de.mysterycode.wcf.toDo', $todo->todoID);
			}
			if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->getHtmlInputProcessor())) {
				$data['hasEmbeddedObjects'] = 1;
			} else {
				$data['hasEmbeddedObjects'] = 0;
			}

			// update note
			$this->getHtmlInputProcessor()->process($todo->note, 'de.mysterycode.wcf.toDo.notes', $todo->todoID, true);
			$data['note'] = $this->getHtmlInputProcessor()->getHtml();
			if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->getHtmlInputProcessor())) {
				$data['notesHasEmbeddedObjects'] = 1;
			} else {
				$data['notesHasEmbeddedObjects'] = 0;
			}

			// update data
			$editor->update($data);
			
			UserActivityEventHandler::getInstance()->fireEvent('de.mysterycode.wcf.toDo.toDo.recentActivityEvent', $todo->todoID, null, $todo->submitter ?: null, $todo->time);
		}
		WCF::getDB()->commitTransaction();
		
		// update activity points
		UserActivityPointHandler::getInstance()->fireEvents('de.mysterycode.wcf.toDo.toDo.activityPointEvent', $userStats, false);
	}

	/**
	 * @return HtmlInputProcessor
	 */
	protected function getHtmlInputProcessor() {
		if ($this->htmlInputProcessor === null) {
			$this->htmlInputProcessor = new HtmlInputProcessor();
		}

		return $this->htmlInputProcessor;
	}
}
