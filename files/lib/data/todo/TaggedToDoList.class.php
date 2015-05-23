<?php
namespace wcf\data\todo;
use wcf\data\tag\Tag;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;

/**
 * Represents a list of accessible todos.
 * 
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class TaggedToDoList extends AccessibleToDoList {
	/**
	 * Creates a object.
	 */
	public function __construct(Tag $tag) {
		parent::__construct();
		
		$this->getConditionBuilder()->add('tag_to_object.objectTypeID = ? AND tag_to_object.languageID = ? AND tag_to_object.tagID = ?', array(TagEngine::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDo'), $tag->languageID, $tag->tagID));
		$this->getConditionBuilder()->add('todo.todoID = tag_to_object.objectID');
	}
	
	/**
	 * @see	\wcf\data\DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_tag_to_object tag_to_object,
				wcf".WCF_N."_todo todo
			".$this->sqlConditionJoins."
			".$this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * @see	\wcf\data\DatabaseObjectList::readObjectIDs()
	 */
	public function readObjectIDs() {
		$this->objectIDs = array();
		$sql = "SELECT	tag_to_object.objectID
			FROM	wcf".WCF_N."_tag_to_object tag_to_object,
				wcf".WCF_N."_todo todo
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