<?php

namespace wcf\system\moderation;
use wcf\data\todo\DeletedToDoList;
use wcf\system\moderation\IDeletedContentProvider;

/**
 * Provides a list of trahed todos
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class DeletedToDoProvider implements IDeletedContentProvider {
	/**
	 * @inheritDoc
	 */
	public function getObjectList() {
		return new DeletedToDoList();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTemplateName() {
		return 'deletedToDoList';
	}
	
	/**
	 * @inheritDoc
	 */
	public function getApplication() {
		return 'wcf';
	}
}
