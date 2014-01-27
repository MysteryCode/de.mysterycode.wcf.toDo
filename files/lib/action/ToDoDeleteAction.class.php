<?php

namespace wcf\action;
use wcf\system\WCF;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\LinkHandler;
use wcf\util\ArrayUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Represents the toDoDelete action.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoDeleteAction extends AbstractAction {

	public $neededPermissions = array('user.toDo.toDo.canDelete');

	public $toDoID = 0;

	/**
	 * @see	wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->toDoID = intval($_REQUEST['id']);
	}

	public function execute() {
		
		if($this->toDoID == 0)
			throw new IllegalLinkException();
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo
			WHERE id = " . $this->toDoID;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$item = $statement->fetchArray();
		
		if(!$item)
			throw new IllegalLinkException();
		
		if(!WCF::getSession()->getPermission('user.toDo.toDo.canDelete') && !(WCF::getSession()->getPermission('user.toDo.toDo.canDeleteOwn') && $item['submitter'] == WCF::getUser()->userID))
			throw new PermissionDeniedException();
		
		$sql = "DELETE FROM wcf" . WCF_N . "_todo
			WHERE id = '" . $this->toDoID . "'";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();

		$sql = "DELETE FROM wcf" . WCF_N . "_todo_to_user
			WHERE toDoID = '" . $this->toDoID . "'";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();

		$this->executed();

		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('ToDoList', array()));
		exit;
	}
}