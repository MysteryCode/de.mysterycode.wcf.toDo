<?php

namespace wcf\system\moderation;
use wcf\data\todo\DeletedToDoList;
use wcf\system\moderation\IDeletedContentProvider;

/**
 * Provides a list of trahed todos
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category	WCF
 */
class DeletedToDoProvider implements IDeletedContentProvider {
	/**
	 * @see	\wcf\system\moderation\IDeletedContentProvider::getObjectList()
	 */
	public function getObjectList() {
		return new DeletedToDoList();
	}
	
	/**
	 * @see	\wcf\system\moderation\IDeletedContentProvider::getTemplateName()
	 */
	public function getTemplateName() {
		return 'deletedTodoList';
	}
	
	/**
	 * @see	\wcf\system\moderation\IDeletedContentProvider::getApplication()
	 */
	public function getApplication() {
		return 'wcf';
	}
}