<?php

namespace wcf\system\page\handler;

use wcf\data\todo\category\RestrictedTodoCategoryNodeList;
use wcf\data\todo\category\TodoCategoryCache;

/**
 * {@inheritDoc}
 */
class ToDoCategoryPageHandler extends AbstractLookupPageHandler {
	/**
	 * @inheritdoc
	 */
	public function getLink($objectID) {
		return TodoCategoryCache::getInstance()->getCategory($objectID)->getLink();
	}

	/**
	 * @inheritdoc
	 */
	public function isValid($objectID) {
		$category = TodoCategoryCache::getInstance()->getCategory($objectID);
		return $category !== null && $category->categoryID;
	}

	/**
	 * @inheritdoc
	 */
	public function isVisible($objectID = null) {
		$category = TodoCategoryCache::getInstance()->getCategory($objectID);
		return $category !== null && $category->canEnter();
	}
	
	/**
	 * @inheritdoc
	 */
	public function lookup($searchString) {
		$categoryList = new RestrictedTodoCategoryNodeList();
		
		$categories = [];
		/** @var \wcf\data\category\CategoryNode $category */
		foreach ($categoryList as $category) {
			if (stristr($category->getTitle(), $searchString)) $categories[$category->categoryID] = $category->getTitle();
		}
		
		return $categories;
	}
}
