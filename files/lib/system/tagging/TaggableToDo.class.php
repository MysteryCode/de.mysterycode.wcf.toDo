<?php

namespace wcf\system\tagging;
use wcf\data\todo\TaggedToDoList;
use wcf\data\tag\Tag;
use wcf\system\tagging\AbstractTaggable;

/**
 * Implementation of ITaggable for todo tagging.
 * 
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class TaggableToDo extends AbstractTaggable {
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
