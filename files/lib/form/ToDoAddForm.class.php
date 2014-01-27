<?php

namespace wcf\form;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

/**
 * Shows the toDoAdd form.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoAddForm extends AbstractForm {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	
	public $neededPermissions = array('user.toDo.toDo.canAdd');
	
	public $description = '';
	public $endTime = 0;
	public $note = '';
	public $responsibles = array();
	public $status = 1;
	public $title = '';
	public $private = 'off';
	public $important = 'off';
	public $category = 0;
	public $newCategory = '';
	
	/**
	 * @see	wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['endTime']) && $_POST['endTime']>0 && $_POST['endTime'] != '') $this->endTime = \DateTime::createFromFormat('Y-m-d H:i', $_POST['endTime'], WCF::getUser()->getTimeZone())->getTimestamp();
		if (isset($_POST['note'])) $this->note = StringUtil::trim($_POST['note']);
		if (isset($_POST['status'])) $this->status = StringUtil::trim($_POST['status']);
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['private'])) $this->private = StringUtil::trim($_POST['private']);
		if (isset($_POST['important'])) $this->important = StringUtil::trim($_POST['important']);
		if (isset($_POST['category'])) $this->category = StringUtil::trim($_POST['category']);
		if (isset($_POST['newCategory'])) $this->newCategory = StringUtil::trim($_POST['newCategory']);
		
		$this->responsibles = $this->validateReponsibles($_POST['responsibles']);
		
		if ($this->newCategory != '' && TODO_CATEGORY_ENABLE) $this->category = $this->createCategory($this->newCategory);
	}

	/**
	 * @see	wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
		
		if (empty($this->description)) {
			throw new UserInputException('description');
		}
		
		if (empty($this->status) && TODO_SET_STATUS_ON_CREATE) {
			throw new UserInputException('status');
		}
		
		if (empty($this->category) && empty($this->newCategory) && TODO_CATEGORY_ENABLE) {
			throw new UserInputException('category');
		}
	}

	/**
	 * @see	wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$timestamp = TIME_NOW;
		
		if($this->private == 'off')
			$privateInt = 0;
		else
			$privateInt = 1;
		
		if($this->important == 'off')
			$importantInt = 0;
		else
			$importantInt = 1;
		
		if($this->endTime == '')
			$this->endTime = 0;
		
		// creat todo
		$sql = "INSERT INTO wcf" . WCF_N . "_todo
			(title, description, note, status, submitter, timestamp, endTime, private, important, category)
			VALUES (
				'" . $this->title . "',
				'" . $this->description . "',
				'" . $this->note . "',
				'" . $this->status . "',
				" . WCF::getUser()->userID . ",
				" . $timestamp . ",
				" . $this->endTime . ",
				" . $privateInt . ",
				" . $importantInt . ",
				" . $this->category . "
			);";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		if(!empty($this->responsibles) && WCF::getSession()->getPermission('user.toDo.responsible.canEdit')) {
			// get id of created todo
			$sql = "SELECT *
				FROM wcf" . WCF_N . "_todo
				WHERE title = '" . $this->title . "'
					AND timestamp = " . $timestamp . "
					AND submitter = " . WCF::getUser()->userID;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute();
			$todo = $statement->fetchArray();
			
			// create responsibles
			for($i=0; $i<count($this->responsibles); $i++) {
				$sql = "INSERT INTO wcf" . WCF_N . "_todo_to_user
					(toDoID, userID)
					VALUES (
						'" . $todo['id'] . "',
						'" . $this->responsibles[$i] . "'
					);";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute();
			}
		}
		
		$this->saved();
		
		$this->title = $this->description = $this->note = $this->endTime = $this->newCategory = '';
		$this->status = 1;
		$this->category = 0;
		$this->private = $this->important = 'off';
		$this->responsibles = array();
		
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}

	/**
	 * @see	wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'title' => $this->title,
			'description' => $this->description,
			'note' => $this->note,
			'status' => $this->status,
			'responsibles' => $this->responsibles,
			'endTime' => $this->endTime,
			'private' => $this->private,
			'important' => $this->important,
			'category' => $this->category,
			'categoryList' => $this->getCategories(),
			'action' => 'add'
		));
	}
	
	public function validateReponsibles($searchArray, array $existingResponsibles = array()) {
		$result = array();
		
		$responsibleList = UserProfile::getUserProfilesByUsername(ArrayUtil::trim(explode(',', $searchArray)));
		
		$userIDs = array();
		foreach ($responsibleList as $user) {
			if ($user) {
				$userIDs[] = $user->userID;
			}
		}
		
		return $userIDs;
	}
	
	public function createCategory($title) {
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category
			WHERE title = '" . $title . "'";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$item = $statement->fetchArray();
		
		if($item)
			return $item['id'];
		
		$sql = "INSERT INTO wcf" . WCF_N . "_todo_category
			(title)
			VAlUES (
				'" . $title . "'
			);";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category
			WHERE title = '" . $title . "'";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$item = $statement->fetchArray();
		
		return $item['id'];
	}
	
	public function getCategories() {
		$categories = array();
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category
			ORDER BY title ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			$categories[] = array(
				'id' => $row["id"],
				'title' => $row["title"]
			);
		}
		return $categories;
	}
}