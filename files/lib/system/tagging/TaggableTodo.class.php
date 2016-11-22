<?php

namespace wcf\system\tagging;
use wcf\data\tag\Tag;
use wcf\data\todo\TaggedToDoList;
use wcf\system\tagging\AbstractTaggable;

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
	 * @see	\wcf\system\tagging\ITaggable::getObjectList()
	 */
	public function getObjectList(Tag $tag) {
		return new TaggedToDoList($tag);
	}
	
	/**
	 * @see	\wcf\system\tagging\ITaggable::getTemplateName()
	 */
	public function getTemplateName() {
		return 'searchResultToDoList';
	}
}
