<?php

namespace wcf\acp\page;
use wcf\data\todo\status\TodoStatusList;
use wcf\page\SortablePage;

/**
 * Shows the field list.
 *
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
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
