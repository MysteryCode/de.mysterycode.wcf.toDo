<?php

namespace wcf\system\user\activity\event;
use wcf\data\comment\CommentList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Shows the todo comment user activity event.
 *
 * @author Florian Gail
 * @copyright 2013 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoCommentUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	public function prepare(array $events) {
		$objectIDs = array();
		foreach ($events as $event ) {
			$objectIDs[] = $event->objectID;
		}
		
		$commentList = new CommentList();
		$commentList->getConditionBuilder()->add("comment.commentID IN (?)", array($objectIDs));
		$commentList->readObjects();
		$comments = $commentList->getObjects();
	}
}