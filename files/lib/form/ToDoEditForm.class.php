<?php

namespace wcf\form;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

/**
 * Shows the toDoEdit form.
 *
 * @author	Florian Gail
 * @copyright	2013 Florian Gail <http://www.mysterycode.de/>
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoEditForm extends AbstractForm {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.header.menu.toDo';
	
	public $description = '';
	public $note = '';
	public $responsibles = array();
	public $status = '';
	public $title = '';
	public $toDoID = 0;
	public $endTime = 0;
	public $private = 'off';
	public $important = 'off';
	public $category = 0;
	public $newCategory = '';
	
	/**
	 * @see	wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->toDoID = intval($_REQUEST['id']);
	}
	
	/**
	 * @see	wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		if (isset($_POST['endTime']) && $_POST['endTime'] > 0 && $_POST['endTime'] != '') $this->endTime = \DateTime::createFromFormat('Y-m-d H:i', $_POST['endTime'], WCF::getUser()->getTimeZone())->getTimestamp();
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
		
		if (empty($this->status)) {
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
		
		$sql = "UPDATE wcf" . WCF_N . "_todo
			SET title = '" . $this->title . "',
				description = '" . $this->description . "',
				note = '" . $this->note . "',
				status = " . $this->status . ",
				endTime = " . $this->endTime . ",
				private = " . $privateInt . ",
				important = " . $importantInt . ",
				category = " . $this->category . ",
				updatetimestamp = " . TIME_NOW . "
			WHERE id = '" . $this->toDoID . "'";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		if(WCF::getSession()->getPermission('user.toDo.responsible.canEdit')) {
			// delete existing responsibles
			$sql = "DELETE FROM wcf" . WCF_N . "_todo_to_user
				WHERE toDoID = " . $this->toDoID;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute();
			
			if(!empty($this->responsibles)) {
				// create responsibles new
				for($i=0; $i<count($this->responsibles); $i++) {
					$sql = "INSERT INTO wcf" . WCF_N . "_todo_to_user
					(toDoID, userID)
					VALUES (
						'" . $this->toDoID . "',
						'" . $this->responsibles[$i] . "'
					);";
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute();
				}
			}
		}
		
		$this->saved();
		
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}

	/**
	 * @see	wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
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
		
		if(!WCF::getSession()->getPermission('user.toDo.toDo.canEdit') && !(WCF::getSession()->getPermission('user.toDo.toDo.canEditOwn') && $item['submitter'] == WCF::getUser()->userID))
			throw new PermissionDeniedException();
		
		$this->title = $item['title'];
		$this->description = $item['description'];
		$this->note = $item['note'];
		$this->responsibles = $this->getResponsible($item['id']);
		$this->status = $item['status'];
		$this->category = $item['category'];
		
		if($item['private'] == 0)
			$this->private = 'off';
		else
			$this->private = 'on';
		
		if($item['important'] == 0)
			$this->important = 'off';
		else
			$this->important = 'on';
		
		if($item['endTime'] > 0)
			$this->endTime = DateUtil::getDateTimeByTimestamp($item['endTime']);
			$this->endTime->setTimezone(WCF::getUser()->getTimeZone());
			$this->endTime = $this->endTime->format('c');
		
	}

	/**
	 * @see	wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'id' => $this->toDoID,
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
			'action' => 'edit'
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
		$a = 0;
		while($responsible = $statement->fetchArray()) {
			$user = new User($responsible['userID']);
			if($user->username != '') {
				$this->responsibles[] = array(
					'id' => $responsible['userID'],
					'username' => $user->username,
					'isLast' => false
				);
				$a++;
			}
		}
		if($a > 0)
			$this->responsibles[$a - 1]['isLast'] = true;
	
		return $this->responsibles;
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