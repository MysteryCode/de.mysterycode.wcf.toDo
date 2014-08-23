<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the toDoCategoryAdd form.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoCategoryAddForm extends AbstractForm {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoCategory.add';
	
	public $neededPermissions = array('admin.content.toDo.category.canAdd');
	
	public $categoryID = 0;
	public $title = '';
	public $color = 'rgba(150, 150, 150, 1)';
	public $isClosed = 0;
	public $isDisabled = 0;
	public $description = '';
	
	/**
	 * @see	wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['color'])) $this->color = StringUtil::trim($_POST['color']);
		if (isset($_POST['description'])) $this->description = StringUtil::trim($_POST['description']);
		
		if (isset($_POST['isClosed'])) $this->isClosed = true;
		if (isset($_POST['isDisabled'])) $this->isDisabled = true;
	}
	
	/**
	 * @see	wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->title)) {
			throw new UserInputException('title');
		}
		if (empty($this->color)) {
			throw new UserInputException('color');
		}
	}
	
	/**
	 * @see	wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$sql = "INSERT INTO wcf" . WCF_N . "_todo_category
			(title, color, isClosed, isDisabled, description)
			VALUES (
				'" . $this->title . "',
				'" . $this->color . "',
				'" . $this->isClosed . "',
				'" . $this->isDisabled . "',
				'" . $this->description . "'
			);";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		$this->saved();
		
		$this->title = $this->color = $this->description = '';
		$this->isClosed = $this->isDisabled = 0;
		
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
			'color' => $this->color,
			'isClosed' => $this->isClosed,
			'isDisabled' => $this->isDisabled,
			'description' => $this->description,
			'action' => 'add'
		));
	}
}