<?php

namespace wcf\form;

use wcf\data\category\Category;
use wcf\data\todo\category\RestrictedTodoCategoryNodeTree;
use wcf\data\todo\category\TodoCategory;
use wcf\data\todo\category\TodoCategoryCache;
use wcf\data\todo\status\TodoStatus;
use wcf\data\todo\status\TodoStatusList;
use wcf\data\todo\ToDo;
use wcf\data\todo\ToDoAction;
use wcf\data\user\UserProfile;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\message\censorship\Censorship;
use wcf\system\label\object\TodoLabelObjectHandler;
use wcf\system\label\LabelHandler;
use wcf\system\message\quote\MessageQuoteManager;
use wcf\system\user\notification\object\ToDoUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the toDoAdd form.
 *
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class TodoAddForm extends MessageForm {
	/**
	 * @inheritDoc
	 */
	public $attachmentObjectType = 'de.mysterycode.wcf.toDo.toDo';

	/**
	 * @inheritDoc
	 */
	public $messageObjectType = 'de.mysterycode.wcf.toDo';

	/**
	 * @inheritDoc
	 */
	public $messageNotesObjectType = 'de.mysterycode.wcf.toDo.notes';
	
	public $enableComments = 1;
	
	public $neededModules = ['TODOLIST'];

	/**
	 * timestamp the todo until has to be solved
	 * @var integer
	 */
	public $endTime = 0;

	/**
	 * notes text
	 * @var string
	 */
	public $note = '';

	/**
	 * responsible users as a string
	 * @var string
	 */
	public $responsibles = '';

	/**
	 * responsible groups as a string
	 * @var string
	 */
	public $responsibleGroups = '';

	/**
	 * id of the status
	 * @var integer
	 */
	public $statusID = 0;

	/**
	 * 1: todo is private
	 * 0: todo is public
	 * @var boolean
	 */
	public $private = 0;

	/**
	 * represents the todo's prio
	 * 1: important
	 * 0: medium
	 * -1: low
	 * @var integer
	 */
	public $important = 0;

	/**
	 * id of the todo's category
	 * @var integer
	 */
	public $categoryID = 0;

	/**
	 * @var TodoCategory
	 */
	public $category = null;

	/**
	 * @var integer
	 */
	public $progress = 0;

	/**
	 * @var integer
	 */
	public $remembertime = 0;

	/**
	 * @var TodoStatus[]
	 */
	public $statusList = [];

	/**
	 * @inheritDoc
	 */
	public $action = 'add';

	/**
	 * @var HtmlInputProcessor
	 */
	public $notesHtmlInputProcessor;

	/**
	 * @var \RecursiveIterator
	 */
	public $categoryList = null;
	
	/**
	 * @var	\wcf\data\label\group\ViewableLabelGroup[]
	 */
	public $labelGroups;
	
	/**
	 * label ids
	 * @var	integer[]
	 */
	public $labelIDs = [];
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		MessageQuoteManager::getInstance()->readParameters();
		
		if (isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
		$this->category = new TodoCategory(new Category($this->categoryID));
		if (!$this->category->categoryID)
			throw new IllegalLinkException();
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['labelIDs']) && is_array($_POST['labelIDs'])) $this->labelIDs = $_POST['labelIDs'];
		
		if (isset($_POST['description'])) $this->text = StringUtil::trim($_POST['description']);
		if (isset($_POST['endTime']) && $_POST['endTime'] > 0 && $_POST['endTime'] != '') $this->endTime = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $_POST['endTime'], WCF::getUser()->getTimeZone())->getTimestamp();
		if (isset($_POST['note'])) $this->note = StringUtil::trim($_POST['note']);
		if (isset($_POST['statusID'])) $this->statusID = StringUtil::trim($_POST['statusID']);
		if (isset($_POST['title'])) $this->subject = StringUtil::trim($_POST['title']);
		if (isset($_POST['private'])) $this->private = 1;
		if (isset($_POST['priority'])) $this->important = StringUtil::trim($_POST['priority']);
		if (isset($_POST['categoryID'])) $this->categoryID = StringUtil::trim($_POST['categoryID']);
		if (isset($_POST['progress'])) $this->progress = StringUtil::trim($_POST['progress']);
		if (isset($_POST['remembertime']) && $_POST['remembertime'] > 0 && $_POST['remembertime'] != '') $this->remembertime = \DateTime::createFromFormat('Y-m-d', $_POST['remembertime'], WCF::getUser()->getTimeZone())->getTimestamp();
		if (isset($_POST['responsibles'])) $this->responsibles = StringUtil::trim($_POST['responsibles']);
		if (isset($_POST['responsibleGroups'])) $this->responsibleGroups = StringUtil::trim($_POST['responsibleGroups']);
		
		if (isset($_POST['enableComments'])) {
			$this->enableComments = 1;
		} else if (!empty($_POST)) {
			$this->enableComments = 0;
		}
		
		MessageQuoteManager::getInstance()->readFormParameters();
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();

		$this->validateNotes();
		
		if (empty($this->statusID) && TODO_SET_STATUS_ON_CREATE && $this->category->canEditStatus()) {
			throw new UserInputException('statusID');
		}
		
		if (empty($this->progress) && TODO_PROGRESS_ENABLE) {
			$this->progress = 0;
		}
		
		if (($this->progress < 0 || $this->progress > 100) && TODO_PROGRESS_ENABLE) {
			throw new UserInputException('progress', 'inValid');
		}
		
		$validationResult = TodoLabelObjectHandler::getInstance()->validateLabelIDs($this->labelIDs, 'canSetLabel', false);
		if (!empty($validationResult[0])) {
			throw new UserInputException('labelIDs');
		}
		
		if (!empty($validationResult)) {
			throw new UserInputException('label', $validationResult);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();

		$this->note = $this->notesHtmlInputProcessor->getHtml();

		$todoData = array_merge([
			'data' => [
				'title' => $this->subject,
				'description' => $this->text,
				'note' => $this->note,
				'submitter' => WCF::getUser()->userID,
				'username' => WCF::getUser()->username,
				'time' => TIME_NOW,
				'endTime' => $this->endTime,
				'private' => $this->private,
				'important' => $this->important,
				'categoryID' => $this->categoryID ?: null,
				'progress' => $this->progress,
				'remembertime' => $this->remembertime,
				'enableComments' => $this->enableComments,
				'hasLabels' => !empty($this->labelIDs) ? 1 : 0
			],
			'attachmentHandler' => $this->attachmentHandler,
			'htmlInputProcessor' => $this->htmlInputProcessor,
			'notesHtmlInputProcessor' => $this->notesHtmlInputProcessor
		], $this->additionalFields);
		
		if (!$this->category->getPermission('user.canAddTodoWithoutModeration')) {
			$todoData['data']['isDisabled'] = 1;
		}
		
		if (!empty($this->statusID))
			$todoData['data']['statusID'] = $this->statusID;
		
		$this->objectAction = new ToDoAction([], 'create', $todoData);
		$resultValues = $this->objectAction->executeAction();
		
		// save labels
		if (!empty($this->labelIDs)) {
			TodoLabelObjectHandler::getInstance()->setLabels($this->labelIDs, $resultValues['returnValues']->todoID);
		}

		if (!empty($this->responsibles)) {
			$responsibleUserAction = new ToDoAction([$resultValues['returnValues']->todoID], 'updateResponsibles', ['search' => $this->responsibles]);
			$responsibleUserAction->executeAction();
		}
		
		if (!empty($this->responsibleGroups)) {
			$responsibleGroupAction = new ToDoAction([$resultValues['returnValues']->todoID], 'updateResponsibleGroups', ['search' => $this->responsibleGroups]);
			$responsibleGroupAction->executeAction();
		}
		
		MessageQuoteManager::getInstance()->saved();
		
		$this->saved();
		
		if ($resultValues['returnValues']->isDisabled && !$this->category->getPermission('mod.canEnableTodos')) {
			HeaderUtil::delayedRedirect($this->category->getLink(), WCF::getLanguage()->get('wcf.toDo.moderation.redirect'), 30);
		} else {
			HeaderUtil::redirect($resultValues['returnValues']->getLink());
		}
		exit;
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		$statusList = new TodoStatusList();
		$statusList->readObjects();
		$this->statusList = $statusList->getObjects();
		
		$categoryNodeTree = new RestrictedTodoCategoryNodeTree();
		$this->categoryList = $categoryNodeTree->getIterator();
		
		$labelGroupIDs = TodoCategoryCache::getInstance()->getLabelGroupIDs($this->categoryID);
		if (!empty($labelGroupIDs)) $this->labelGroups = LabelHandler::getInstance()->getLabelGroups($labelGroupIDs);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		MessageQuoteManager::getInstance()->assignVariables();
		
		WCF::getTPL()->assign( [
			'title' => $this->subject,
			'description' => $this->text,
			'note' => $this->note,
			'statusID' => $this->statusID,
			'responsibles' => $this->responsibles,
			'responsibleGroups' => $this->responsibleGroups,
			'endTime' => $this->endTime,
			'private' => $this->private,
			'important' => $this->important,
			'categoryID' => $this->categoryID,
			'category' => $this->category,
			'categoryList' => $this->categoryList,
			'progress' => $this->progress,
			'remembertime' => $this->remembertime,
			'allowedFileExtensions' => explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.toDo.attachment.allowedAttachmentExtensions'))),
			'statusList' => $this->statusList,
			'enableComments' => $this->enableComments,
			'labelGroups' => $this->labelGroups,
			'labelIDs' => $this->labelIDs
		]);
	}
	
	public function updateResponsibles($todoID = 0, $search, $existingResponsibles = []) {
		if ($todoID == 0)
			return null;
		
		$responsibleList = UserProfile::getUserProfilesByUsername(ArrayUtil::trim(explode(',', $search)));
		
		$responsibleList = array_unique($responsibleList);
		
		$userIDs = [];
		$checkArray = [];
		foreach ($responsibleList as $user) {
			if ($user && $user !== null && !in_array($user->userID, $existingResponsibles)) {
				$userIDs[] = $user->userID;
				$sql = "INSERT INTO wcf" . WCF_N . "_todo_to_user
					(toDoID, userID, username)
					VAlUES(?, ?, ?);";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute([$todoID, $user->userID, $user->username]);
				
				$checkArray[] = $user->userID;
			}
		}
		
		if ($todoID != 0) {
			foreach ($existingResponsibles as $responsible) {
				if (!in_array($responsible, $checkArray)) {
					$sql = "DELETE FROM wcf" . WCF_N . "_todo_to_user
						WHERE toDoID = ?
							AND userID = ?";
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute([$todoID, $responsible]);
				}
			}
		}
		
		if (!empty($userIDs))
			UserNotificationHandler::getInstance()->fireEvent('assign', 'de.mysterycode.wcf.toDo.toDo.notification', new ToDoUserNotificationObject(new ToDo($todoID)), $userIDs);
	}

	/**
	 * @see MessageForm::validateText()
	 */
	protected function validateNotes() {
		if (empty($this->messageNotesObjectType)) {
			throw new \RuntimeException("Expected non-empty message object type for '".get_class($this)."'");
		}

		if ($this->disallowedBBCodesPermission) {
			BBCodeHandler::getInstance()->setDisallowedBBCodes(explode(',', WCF::getSession()->getPermission($this->disallowedBBCodesPermission)));
		}

		$this->notesHtmlInputProcessor = new HtmlInputProcessor();
		$this->notesHtmlInputProcessor->process($this->note, $this->messageNotesObjectType, 0);

		// check text length
		$message = $this->notesHtmlInputProcessor->getTextContent();
		if ($this->maxTextLength != 0 && mb_strlen($message) > $this->maxTextLength) {
			throw new UserInputException('note', 'tooLong');
		}

		$disallowedBBCodes = $this->notesHtmlInputProcessor->validate();
		if (!empty($disallowedBBCodes)) {
			WCF::getTPL()->assign('disallowedBBCodes', $disallowedBBCodes);
			throw new UserInputException('note', 'disallowedBBCodes');
		}

		// search for censored words
		if (ENABLE_CENSORSHIP) {
			$result = Censorship::getInstance()->test($message);
			if ($result) {
				WCF::getTPL()->assign('censoredWords', $result);
				throw new UserInputException('note', 'censoredWordsFound');
			}
		}
	}
}
