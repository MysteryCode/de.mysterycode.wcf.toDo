<?php

namespace wcf\system\tagging;
use wcf\data\tag\Tag;
use wcf\data\todo\TaggedToDoList;

/**
 * Implementation of ITaggable for todo tagging.
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TaggableTodo extends AbstractTaggable {
	/**
	 * @inheritDoc
	 */
	public function getObjectList(Tag $tag) {
		return new TaggedToDoList($tag);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTemplateName() {
		return 'searchResultToDoList';
	}
}
