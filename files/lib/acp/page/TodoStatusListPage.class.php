<?php

namespace wcf\acp\page;
use wcf\page\SortablePage;

/**
 * Shows the field list.
 *
 * @author	Florian Gail
 * @copyright	2014-2015 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenpflichtige Produkte <http://downloads.mysterycode.de/index.php/License/4-Kostenpflichtige-Produkte/>
 * @package	de.mysterycode.wcf.inventar
 * @category	INVENTAR
 */
class TodoStatusListPage extends SortablePage {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoStatus.list';
	
	/**
	 * @see	\wcf\page\SortablePage::$defaultSortField
	 */
	public $defaultSortField = 'showOrder';
	
	/**
	 * @see	\wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array('subject', 'showOrder');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.content.toDo.status.canEdit', 'admin.content.toDo.status.canDelete');
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'wcf\data\todo\status\TodoStatusList';
}
