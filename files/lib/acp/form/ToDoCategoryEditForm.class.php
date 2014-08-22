<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the toDoCategoryEdit form.
 *
 * @author	Florian Gail
 * @copyright	2014 Florian Gail <http://www.mysterycode.de/>
 * @license	Kostenlose Plugins <http://downloads.mysterycode.de/index.php/License/6-Kostenlose-Plugins/>
 * @package	de.mysterycode.wcf.toDo
 * @category 	WCF
 */

class ToDoCategoryEditForm extends AbstractForm {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.toDoCategory.list';
	
	public $neededPermissions = array('admin.content.toDo.category.canEdit');
	
	public $categoryID = 0;
	public $title = '';
	public $color = '';
	
	/**
	 * @see	wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
	
		if (isset($_REQUEST['id'])) $this->categoryID = intval($_REQUEST['id']);
	}
	
	/**
	 * @see	wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['color'])) $this->color = StringUtil::trim($_POST['color']);
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
			throw new UserInputException('author');
		}
	}
	
	/**
	 * @see	wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$sql = "UPDATE wcf" . WCF_N . "_todo_category
				SET title = '" . $this->title . "',
					color = '" . $this->color . "'
				WHERE id = " . $this->categoryID . ";";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
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
		
		if($this->categoryID == 0)
			throw new IllegalLinkException();
		
		$sql = "SELECT *
			FROM wcf" . WCF_N . "_todo_category
			WHERE id = '" . $this->categoryID . "'";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$category = $statement->fetchArray();
		
		if(!$category)
			throw new IllegalLinkException();
		
		$this->title = $category['title'];
		$this->color = $category['color'];
	}
	
	/**
	 * @see	wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'id' => $this->categoryID,
			'title' => $this->title,
			'color' => $this->color,
			'action' => 'edit'
		));
	}
}