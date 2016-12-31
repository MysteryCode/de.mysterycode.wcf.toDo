<?php

namespace wcf\page;
use wcf\data\todo\category\RestrictedTodoCategoryNodeList;
use wcf\data\user\group\UserGroupSearchAction;
use wcf\data\user\online\UsersOnlineList;
use wcf\data\user\UserProfile;
use wcf\system\like\LikeHandler;
use wcf\system\todo\ToDoHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the todo list page.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
abstract class AbstractToDoListPage extends SortablePage {
	/**
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	
	/**
	 * @see \wcf\page\SortablePage::$defaultSortField
	 */
	public $defaultSortField = TODO_DEFAULT_SORT_FIELD;
	
	/**
	 * @see \wcf\page\SortablePage::$defaultSortOrder
	 */
	public $defaultSortOrder = TODO_DEFAULT_SORT_ORDER;
	
	/**
	 * @see \wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array(
		'statusID',
		'categoryID',
		'title',
		'submitTime',
		'endTime',
		'submitter',
		'timestamp',
		'updatetimestamp',
		'important',
		'remembertime' 
	);
	
	/**
	 * @see \wcf\page\MultipleLinkPage::$itemsPerPage
	 */
	public $itemsPerPage = TODO_TODOS_PER_PAGE;
	
	/**
	 * @see \wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'wcf\data\todo\ViewableToDoList';
	
	public $neededModules = array('TODOLIST');
	
	public $neededPermissions = array('user.toDo.toDo.canView');
	
	/**
	 * @see	\wcf\page\AbstractPage::$enableTracking
	 */
	public $enableTracking = true;
	
	/**
	 * like data for posts
	 * @var	array<\wcf\data\like\object\LikeObject>
	 */
	public $likeData = array();
	
	/**
	 * category node list
	 * @var	\wcf\data\todo\category\RestrictedTodoCategoryNodeList
	 */
	public $categoryNodeList = null;

	/**
	 * filter for username or groupname
	 * @var string
	 */
	public $responsibleFilter = '';
	
	/**
	 * filter for status
	 * @var string
	 */
	public $statusFilter = null;
	
	/**
	 * amount of all todos
	 * @var integer
	 */
	public $todoCount = 0;
	
	/**
	 * amount of todos in progress
	 */
	public $inProgressCount = 0;
	
	/**
	 * amount of finished todos
	 * @var integer
	 */
	public $finishedCount = 0;
	
	/**
	 * @see \wcf\page\SortablePage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (!empty($_POST['responsibleFilter'])) $this->responsibleFilter = StringUtil::trim($_POST['responsibleFilter']);
		if (!empty($_POST['statusFilter'])) $this->statusFilter = StringUtil::trim($_POST['statusFilter']);
	}
	
	/**
	 * @see \wcf\page\SortablePage::validateSortField()
	 */
	public function validateSortField() {
		parent::validateSortField();

		if ($this->sortField == 'status')
			$this->sortField = 'statusID';

		if ($this->sortField == 'category')
			$this->sortField = 'categoryID';
	}
	
	/**
	 * @see \wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		if (!empty($this->responsibleFilter)) {
			$user = UserProfile::getUserProfileByUsername($this->responsibleFilter);
			if (!empty($user)) {
				$this->objectList->getConditionBuilder()->add('todo_table.todoID IN (SELECT todo_user.todoID FROM wcf' . WCF_N . '_todo_to_user todo_user WHERE todo_user.userID = ?)', array($user->userID));
			} else {
				$group = new UserGroupSearchAction(array(), 'getSearchResultList', array('data' => array('searchString' => $this->responsibleFilter)));
				$group = $group->executeAction();
				$group = reset($group['returnValues']);
				
				if (!empty($group)) {
					$this->objectList->getConditionBuilder()->add('todo_table.todoID IN (SELECT todo_group.todoID FROM wcf' . WCF_N . '_todo_to_group todo_group WHERE todo_group.groupID = ?)', array(intval($group['objectID'])));
				}
			}
		}
		
		if (!empty($this->statusFilter)) {
			$this->objectList->getConditionBuilder()->add('statusID = ?', array($this->statusFilter));
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		// init category node list
		$this->categoryNodeList = new RestrictedTodoCategoryNodeList();
		
		// users online
		if (MODULE_USERS_ONLINE) {
			// init users online list
			$this->usersOnlineList = new UsersOnlineList();
			$this->usersOnlineList->readStats();
			$this->usersOnlineList->checkRecord();
			$this->usersOnlineList->getConditionBuilder()->add('session.userID IS NOT NULL');
			$this->usersOnlineList->readObjects();
		}
		
		// fetch likes
		if (MODULE_LIKE) {
			$todoIDs = array();
			foreach ($this->objectList as $todo) {
				$todoIDs[] = $todo->todoID;
			}
			$objectType = LikeHandler::getInstance()->getObjectType('de.mysterycode.wcf.toDo.toDo');
			LikeHandler::getInstance()->loadLikeObjects($objectType, $todoIDs);
			$this->likeData = LikeHandler::getInstance()->getLikeObjects($objectType);
		}
		
		$this->todoCount = ToDoHandler::getInstance()->getStats('todos');
		$this->inProgressCount = ToDoHandler::getInstance()->getStats('todosInProgress');
		$this->finishedCount = ToDoHandler::getInstance()->getStats('todosFinished');
	}
	
	/**
	 * @see wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'likeData' => $this->likeData,
			'categoryNodeList' => $this->categoryNodeList,
			'usersOnlineList' => $this->usersOnlineList,
			'responsibleFilter' => $this->responsibleFilter,
			'statusFilter', $this->statusFilter,
			
			'stats' => array(
				'total' => $this->todoCount,
				'inProgress' => $this->inProgressCount,
				'finished' => $this->finishedCount
			)
		));
	}
}
