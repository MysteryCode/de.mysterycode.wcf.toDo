<?php

namespace wcf\data\todo\assigned\group;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes todo status related actions.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class AssignedGroupAction extends AbstractDatabaseObjectAction {
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\todo\assigned\group\AssignedGroupEditor';
}
