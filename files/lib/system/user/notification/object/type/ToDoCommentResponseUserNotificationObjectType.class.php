<?php
namespace wcf\system\user\notification\object\type;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

/**
 * Shows the todo comment response user notification objecttype.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class ToDoCommentResponseUserNotificationObjectType extends AbstractUserNotificationObjectType {
	protected static $decoratorClassName = 'wcf\system\user\notification\object\CommentResponseUserNotificationObject';
	protected static $objectClassName = 'wcf\data\comment\response\CommentResponse';
	protected static $objectListClassName = 'wcf\data\comment\response\CommentResponseList';
}