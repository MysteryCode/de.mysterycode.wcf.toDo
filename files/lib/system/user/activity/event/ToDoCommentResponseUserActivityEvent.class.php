<?php
namespace wcf\system\user\activity\event;
use wcf\data\comment\CommentList;
use wcf\data\comment\response\CommentResponseList;
use wcf\data\user\User;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Shows the todo comment response user activity event.
 *
 * @author Florian Gail
 * @copyright 2013 Florian Gail <http://www.mysterycode.de/>
 * @license Creative Commons <by-nc-nd> <http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode>
 * @package de.mysterycode.wcf.toDo
 * @category WCF
 */
class ToDoCommentResponseUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	public function prepare(array $events) {
		$objectIDs = array();
		foreach($events as $event) {
			$objectIDs[] = $event->objectID;
		}
		
		// comments responses
		$responseList = new CommentResponseList();
		$responseList->getConditionBuilder()->add("comment_response.responseID IN(?)", array($objectIDs));
		$responseList->readObjects();
		$responses = $responseList->getObjects();
		
		// comments
		$commentIDs = array();
		foreach($responses as $response) {
			$commentIDs[] = $response->commentID;
		}
		$commentList = new CommentList();
		$commentList->getConditionBuilder()->add("comment.commentID IN(?)", array($commentIDs));
		$commentList->readObjects();
		$comments = $commentList->getObjects();
	}
}