<?php
namespace wcf\system\user\notification\object\type;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

/**
 * Shows the toDo comment response user notification objecttype.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoCommentResponseUserNotificationObjectType extends AbstractUserNotificationObjectType {
	protected static $decoratorClassName = 'wcf\system\user\notification\object\CommentResponseUserNotificationObject';
	protected static $objectClassName = 'wcf\data\comment\response\CommentResponse';
	protected static $objectListClassName = 'wcf\data\comment\response\CommentResponseList';
}