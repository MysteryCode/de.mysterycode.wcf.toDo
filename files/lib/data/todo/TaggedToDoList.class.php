<?php
namespace wcf\data\todo;
use wcf\data\tag\Tag;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;

/**
 * Represents a list of accessible todos.
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TaggedToDoList extends AccessibleToDoList {
	/**
	 * Creates a object.
	 *
	 * @param Tag $tag
	 * @throws \wcf\system\exception\SystemException
	 */
	public function __construct(Tag $tag) {
		parent::__construct();
		
		$this->getConditionBuilder()->add('tag_to_object.objectTypeID = ? AND tag_to_object.languageID = ? AND tag_to_object.tagID = ?', [TagEngine::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo'), $tag->languageID, $tag->tagID]);
		$this->getConditionBuilder()->add('todo_table.todoID = tag_to_object.objectID');
	}
	
	/**
	 * @inheritDoc
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_tag_to_object tag_to_object,
				".$this->getDatabaseTableName()." ".$this->getDatabaseTableAlias()."
			".$this->sqlConditionJoins."
			".$this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * @inheritDoc
	 */
	public function readObjectIDs() {
		$this->objectIDs = [];
		$sql = "SELECT	tag_to_object.objectID
			FROM	wcf".WCF_N."_tag_to_object tag_to_object,
				".$this->getDatabaseTableName()." ".$this->getDatabaseTableAlias()."
				".$this->sqlConditionJoins."
				".$this->getConditionBuilder()."
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$statement->execute($this->getConditionBuilder()->getParameters());
		
		while ($row = $statement->fetchArray()) {
			$this->objectIDs[] = $row['objectID'];
		}
	}
}
