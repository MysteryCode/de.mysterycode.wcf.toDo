<?php
namespace wcf\data\todo;
use wcf\data\edit\history\entry\EditHistoryEntry;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\edit\IHistorySavingObject;
use wcf\system\WCF;

/**
 * History Saving Todos are todos that can be reverted to an older version.
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class HistorySavingToDo extends DatabaseObjectDecorator implements IHistorySavingObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = 'wcf\data\todo\ToDo';
	
	/**
	 * userID of the last editor
	 * @var	integer
	 */
	public $userID = 0;
	
	/**
	 * username of the last editor
	 * @var	string
	 */
	public $username = '';
	
	/**
	 * last edit time
	 * @var	integer
	 */
	public $time = 0;
	
	/**
	 * reason for the last edit
	 * @var	string
	 */
	public $reason = '';
	
	/**
	 * Fetches the information of the latest edit.
	 * 
	 * @inheritDoc
	 */
	public function __construct(DatabaseObject $object) {
		parent::__construct($object);
		$objectTypeID = ObjectTypeCache::getInstance()->getObjectTypeIDByName('com.woltlab.wcf.modifiableContent', 'de.mysterycode.wcf.toDo');
		
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_modification_log
			WHERE		objectTypeID = ?
				AND	objectID = ?
				AND	action = ?
			ORDER BY time DESC, logID DESC";
		$statement = WCF::getDB()->prepareStatement($sql, 1);
		$statement->execute(array($objectTypeID, $this->getDecoratedObject()->todoID, 'edit'));
		$row = $statement->fetchArray();
		
		if ($row) {
			$this->userID = $row['userID'];
			$this->username = $row['username'];
			$this->time = $row['time'];
			$additionalData = @unserialize($row['additionalData']);
			if (isset($additionalData['reason'])) {
				$this->reason = $additionalData['reason'];
			}
			else {
				$this->reason = '';
			}
		}
		else {
			$this->userID = $this->getDecoratedObject()->getUserID();
			$this->username = $this->getDecoratedObject()->getUsername();
			$this->time = $this->getDecoratedObject()->getTime();
			$this->reason = '';
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->getDecoratedObject()->getTitle();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		return $this->getDecoratedObject()->getLink();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getUsername() {
		return $this->username;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getUserID() {
		return $this->userID;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTime() {
		return $this->time;
	}
	
	/**
	 * @inheritDoc
	 */
	public function revertVersion(EditHistoryEntry $edit) {
		$todoAction = new ToDoAction(array($this->getDecoratedObject()), 'update', array(
			'isEdit' => true,
			'data' => array(
				'message' => $edit->message,
				'editReason' => WCF::getLanguage()->getDynamicVariable('wcf.edit.reverted', array('edit' => $edit))
			)
		));
		$todoAction->executeAction();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getEditReason() {
		return $this->reason;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getMessage() {
		return $this->getDecoratedObject()->getMessage();
	}

	/**
	 * Sets the page location data.
	 */
	public function setLocation() {
		// TODO: Implement setLocation() method.
	}
}
