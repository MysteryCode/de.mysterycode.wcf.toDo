<?php

namespace wcf\page;
use wcf\data\todo\category\RestrictedTodoCategoryNodeList;
use wcf\data\todo\status\TodoStatusCache;
use wcf\data\todo\AccessibleToDoList;
use wcf\data\user\group\UserGroupSearchAction;
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
abstract class AbstractTodoListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = TODO_DEFAULT_SORT_FIELD;
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortOrder = TODO_DEFAULT_SORT_ORDER;
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = [
		'statusID',
		'categoryID',
		'title',
		'submitTime',
		'endTime',
		'submitter',
		'time',
		'updatetimestamp',
		'important',
		'remembertime'
	];
	
	/**
	 * @inheritDoc
	 */
	public $itemsPerPage = TODO_TODOS_PER_PAGE;
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = AccessibleToDoList::class;
	
	public $neededModules = ['TODOLIST'];
	
	public $neededPermissions = ['user.toDo.toDo.canView'];
	
	/**
	 * @inheritDoc
	 */
	public $enableTracking = true;
	
	/**
	 * like data for posts
	 * @var	array<\wcf\data\like\object\LikeObject>
	 */
	public $likeData = [];
	
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
	 * list of available status
	 * @var \wcf\data\todo\status\TodoStatus[]
	 */
	public $statusList = [];
	
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
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (!empty($_POST['responsibleFilter'])) $this->responsibleFilter = StringUtil::trim($_POST['responsibleFilter']);
		if (!empty($_POST['statusFilter'])) $this->statusFilter = intval($_POST['statusFilter']);
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateSortField() {
		parent::validateSortField();

		if ($this->sortField == 'status')
			$this->sortField = 'statusID';

		if ($this->sortField == 'category')
			$this->sortField = 'categoryID';
	}
	
	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		if (!empty($this->responsibleFilter)) {
			$user = UserProfile::getUserProfileByUsername($this->responsibleFilter);
			if (!empty($user)) {
				$this->objectList->getConditionBuilder()->add('todo_table.todoID IN (SELECT todo_user.todoID FROM wcf' . WCF_N . '_todo_to_user todo_user WHERE todo_user.userID = ?)', [$user->userID]);
			} else {
				$group = new UserGroupSearchAction([], 'getSearchResultList', ['data' => ['searchString' => $this->responsibleFilter]]);
				$group = $group->executeAction();
				$group = reset($group['returnValues']);
				
				if (!empty($group)) {
					$this->objectList->getConditionBuilder()->add('todo_table.todoID IN (SELECT todo_group.todoID FROM wcf' . WCF_N . '_todo_to_group todo_group WHERE todo_group.groupID = ?)', [intval($group['objectID'])]);
				}
			}
		}
		
		if (!empty($this->statusFilter)) {
			$this->objectList->getConditionBuilder()->add('statusID = ?', [$this->statusFilter]);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		// init category node list
		$this->categoryNodeList = new RestrictedTodoCategoryNodeList();

		// fetch likes
		if (MODULE_LIKE) {
			$todoIDs = [];
			foreach ($this->objectList as $todo) {
				$todoIDs[] = $todo->todoID;
			}
			$objectType = LikeHandler::getInstance()->getObjectType('de.mysterycode.wcf.toDo.toDo.like');
			LikeHandler::getInstance()->loadLikeObjects($objectType, $todoIDs);
			$this->likeData = LikeHandler::getInstance()->getLikeObjects($objectType);
		}
		
		$this->statusList = TodoStatusCache::getInstance()->getStatusList();
		$this->todoCount = ToDoHandler::getInstance()->getStats('todos');
		$this->inProgressCount = ToDoHandler::getInstance()->getStats('todosInProgress');
		$this->finishedCount = ToDoHandler::getInstance()->getStats('todosFinished');
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
			'likeData' => $this->likeData,
			'categoryNodeList' => $this->categoryNodeList,
			'responsibleFilter' => $this->responsibleFilter,
			'statusFilter' => $this->statusFilter,
			'statusList' => $this->statusList,
			
			'stats' => [
				'total' => $this->todoCount,
				'inProgress' => $this->inProgressCount,
				'finished' => $this->finishedCount
			]
		]);
	}
}
