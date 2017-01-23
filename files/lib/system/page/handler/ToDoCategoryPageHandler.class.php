<?php

namespace wcf\system\page\handler;

use wcf\data\todo\category\TodoCategoryCache;

/**
 * {@inheritDoc}
 */
class ToDoCategoryPageHandler extends AbstractMenuPageHandler {
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
}
