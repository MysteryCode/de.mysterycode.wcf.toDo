<?php

namespace wcf\system\user\notification\object\type;


use wcf\system\WCF;

/**
 * Shows the todo comment user notification objecttype.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCommentUserNotificationObjectType extends AbstractUserNotificationObjectType implements ICommentUserNotificationObjectType {
	protected static $decoratorClassName = 'wcf\system\user\notification\object\CommentUserNotificationObject';
	protected static $objectClassName = 'wcf\data\comment\Comment';
	protected static $objectListClassName = 'wcf\data\comment\CommentList';

	public function getOwnerID($objectID) {
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_comment comment
			LEFT JOIN wcf" . WCF_N . "_todo todo
			ON (todo.todoID = comment.objectID)
			WHERE comment.commentID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$objectID
		));
		$row = $statement->fetchArray();
		
		return ($row ? $row['submitter'] : 0);
	}
}
