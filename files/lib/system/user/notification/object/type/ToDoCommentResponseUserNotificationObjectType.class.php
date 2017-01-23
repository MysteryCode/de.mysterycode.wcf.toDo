<?php

namespace wcf\system\user\notification\object\type;
use wcf\data\comment\response\CommentResponse;
use wcf\data\comment\response\CommentResponseList;
use wcf\system\user\notification\object\CommentResponseUserNotificationObject;

/**
 * Shows the todo comment response user notification objecttype.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoCommentResponseUserNotificationObjectType extends AbstractUserNotificationObjectType {
	protected static $decoratorClassName = CommentResponseUserNotificationObject::class;
	protected static $objectClassName = CommentResponse::class;
	protected static $objectListClassName = CommentResponseList::class;
}
