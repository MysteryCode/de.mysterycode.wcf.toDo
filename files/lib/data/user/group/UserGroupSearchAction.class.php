<?php
namespace wcf\data\user\group;
use wcf\data\ISearchAction;
use wcf\system\exception\UserInputException;

/**
 *
 * 
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class UserGroupSearchAction extends UserGroupAction implements ISearchAction {
	/**
	 * @inheritDoc
	 */
	protected $allowGuestAccess = ['getSearchResultList'];
	
	/**
	 * @inheritDoc
	 */
	public function validateGetSearchResultList() {
		$this->readString('searchString', false, 'data');
	
		if (isset($this->parameters['data']['excludedSearchValues']) && !is_array($this->parameters['data']['excludedSearchValues'])) {
			throw new UserInputException('excludedSearchValues');
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function getSearchResultList() {
		$searchString = $this->parameters['data']['searchString'];
		$excludedSearchValues = [];
		if (isset($this->parameters['data']['excludedSearchValues'])) {
			$excludedSearchValues = $this->parameters['data']['excludedSearchValues'];
		}
		$list = [];
		
		$accessibleGroups = UserGroup::getAccessibleGroups();
		foreach ($accessibleGroups as $group) {
			$groupName = $group->getName();
			if (!in_array($groupName, $excludedSearchValues)) {
				$pos = mb_strripos($groupName, $searchString);
				if ($pos !== false && $pos == 0) {
					$list[] = [
							'label' => $groupName,
							'objectID' => $group->groupID,
							'type' => 'group'
					];
				}
			}
		}
		
		return $list;
	}
}
