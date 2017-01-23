<?php

namespace wcf\acp\page;
use wcf\data\todo\status\TodoStatusList;
use wcf\page\SortablePage;

/**
 * Shows the field list.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoStatusListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.todoStatus.list';
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'showOrder';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['subject', 'showOrder'];
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.content.toDo.status.canEdit', 'admin.content.toDo.status.canDelete'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = TodoStatusList::class;
}
