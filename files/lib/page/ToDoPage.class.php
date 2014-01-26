<?php
namespace wcf\page;
use wcf\data\user\User;
use wcf\system\comment\CommentHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the toDo detail page.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoPage extends AbstractPage {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	
	public $neededPermissions = array('user.toDo.toDo.canViewDetail');
	
	public $toDoID = 0;
	public $commentManager = null;
	public $commentList = null;
	public $objectType = 0;
	
	/**
	 * @see	wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
	
		if (isset($_REQUEST['id'])) $this->toDoID = intval($_REQUEST['id']);
	}
	
	/**
	 * @see	wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->objectTypeID = CommentHandler::getInstance()->getObjectTypeID('de.mysterycode.wcf.toDo.toDoComment');
		$objectType = CommentHandler::getInstance()->getObjectType($this->objectTypeID);
		$this->commentManager = $objectType->getProcessor();
		$this->commentList = CommentHandler::getInstance()->getCommentList($this->commentManager, $this->objectTypeID, $this->toDoID);
	}
	
	/**
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		if($this->toDoID == 0)
			throw new IllegalLinkException();
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo
			WHERE id = " . $this->toDoID;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$todo = $statement->fetchArray();
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category
			WHERE id = " . $todo['category'];
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$category = $statement->fetchArray();
		
		if(!$category)
			$category = array(
				'id' => 0,
				'title' => WCF::getLanguage()->get('wcf.toDo.category.notAvailable'),
				'color' => 'gray'
			);
		
		$submitter = new User($todo['submitter']);
		
		if(!$todo)
			throw new IllegalLinkException();
		
		if($todo['private'] == 1 && $todo['submitter'] != WCF::getUser()->userID)
			throw new PermissionDeniedException();
		
		WCF::getTPL()->assign(array(
			'submitterusername' => $submitter->username,
			'responsibles' => $this->getResponsible($todo['id']),
			'categoryname' => $category['title'],
			'categorycolor' => $category['color'],
			'commentList' => $this->commentList,
			'commentObjectTypeID'=> $this->objectTypeID,
                           'commentCanAdd' => $this->commentManager->canAdd($this->toDoID),
                           'lastCommentTime' => $this->commentList->getMinCommentTime(),
                           'commentsPerPage' => $this->commentManager->getCommentsPerPage(),
			'likeData' => (MODULE_LIKE ? $this->commentList->getLikeData() : array()),
                           'toDo' => $todo
		));
	}
	
	public function getResponsible($taskID) {
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_to_user
			WHERE toDoID = " . $taskID . "
			ORDER BY userID ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		
		$this->responsibles = array();
		$i = 1;
		while($responsible = $statement->fetchArray()) {
			$user = new User($responsible['userID']);
			$this->responsibles[$i] = array(
				'id' => $responsible['userID'],
				'username' => $user->username
			);
			$i++;
		}
		
		return $this->responsibles;
	}
	

	public function getObjectType() {
		return 'de.mysterycode.wcf.toDo';
	}
	
	public function getObjectID() {
		return $this->link->linkID;
	}
}