<?php
namespace wcf\system\attachment;



use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * 
 * 
 * @author	Florian Gail <https://www.mysterycode.de/>
 * @copyright	2014-2018 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://www.mysterycode.de/licenses/kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoAttachmentObjectType extends AbstractAttachmentObjectType {
	/**
	 * {@inheritDoc}
	 */
	public function getMaxSize() {
		return WCF::getSession()->getPermission('user.toDo.attachment.attachmentMaxSize');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAllowedExtensions() {
		return ArrayUtil::trim(explode("\n", WCF::getSession()->getPermission('user.toDo.attachment.allowedAttachmentExtensions')));
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMaxCount() {
		return WCF::getSession()->getPermission('user.toDo.attachment.maxAttachmentCount');
	}

	/**
	 * {@inheritDoc}
	 */
	public function canDownload($objectID) {
		return WCF::getSession()->getPermission('user.toDo.attachment.canDownloadAttachments');
	}

	/**
	 * {@inheritDoc}
	 */
	public function canViewPreview($objectID) {
		return (WCF::getSession()->getPermission('user.toDo.attachment.canDownloadAttachments') || WCF::getSession()->getPermission('user.toDo.attachment.canViewAttachmentPreview'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function canUpload($objectID, $parentObjectID = 0) {
		return WCF::getSession()->getPermission('user.toDo.attachment.canUploadAttachment');
	}

	/**
	 * {@inheritDoc}
	 */
	public function canDelete($objectID) {
		return WCF::getSession()->getPermission('user.toDo.attachment.canUploadAttachment');
	}
}
