<?php
namespace wcf\data\user\group;
use wcf\data\ISearchAction;
use wcf\system\exception\UserInputException;

class UserGroupSearchAction extends UserGroupAction implements ISearchAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$allowGuestAccess
	 */
	protected $allowGuestAccess = array('getSearchResultList');
	
	/**
	 * @see	\wcf\data\ISearchAction::validateGetSearchResultList()
	 */
	public function validateGetSearchResultList() {
		$this->readString('searchString', false, 'data');
	
		if (isset($this->parameters['data']['excludedSearchValues']) && !is_array($this->parameters['data']['excludedSearchValues'])) {
			throw new UserInputException('excludedSearchValues');
		}
	}
	
	/**
	 * @see	\wcf\data\ISearchAction::getSearchResultList()
	 */
	public function getSearchResultList() {
		$searchString = $this->parameters['data']['searchString'];
		$excludedSearchValues = array();
		if (isset($this->parameters['data']['excludedSearchValues'])) {
			$excludedSearchValues = $this->parameters['data']['excludedSearchValues'];
		}
		$list = array();
		
		$accessibleGroups = UserGroup::getAccessibleGroups();
		foreach ($accessibleGroups as $group) {
			$groupName = $group->getName();
			if (!in_array($groupName, $excludedSearchValues)) {
				$pos = mb_strripos($groupName, $searchString);
				if ($pos !== false && $pos == 0) {
					$list[] = array(
							'label' => $groupName,
							'objectID' => $group->groupID,
							'type' => 'group'
					);
				}
			}
		}
		
		return $list;
	}
}
