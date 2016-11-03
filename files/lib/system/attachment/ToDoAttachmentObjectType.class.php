<?php
namespace wcf\system\attachment;

use wcf\data\todo\ToDo;
use wcf\system\attachment\AbstractAttachmentObjectType;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * 
 * 
 * @author		Florian Gail
 * @copyright	2014-2016 Florian Gail <https://www.mysterycode.de/>
 * @license	Kostenlose Plugins <https://downloads.mysterycode.de/license/6-kostenlose-plugins/>
 * @package	de.mysterycode.wcf.toDo
 */
class ToDoAttachmentObjectType extends AbstractAttachmentObjectType {
	public function getMaxSize() {
		return WCF::getSession()->getPermission('user.toDo.attachment.attachmentMaxSize');
	}
	
	public function getAllowedExtensions() {
		return ArrayUtil::trim(explode("\n", WCF::getSession()->getPermission('user.toDo.attachment.allowedAttachmentExtensions')));
	}
	
	public function getMaxCount() {
		return WCF::getSession()->getPermission('user.toDo.attachment.maxAttachmentCount');
	}
	
	public function canDownload($objectID) {
		return WCF::getSession()->getPermission('user.toDo.attachment.canDownloadAttachments');
	}
	
	public function canViewPreview($objectID) {
		return (WCF::getSession()->getPermission('user.toDo.attachment.canDownloadAttachments') || WCF::getSession()->getPermission('user.toDo.attachment.canViewAttachmentPreview'));
	}
	
	public function canUpload($objectID, $parentObjectID = 0) {
		return WCF::getSession()->getPermission('user.toDo.attachment.canUploadAttachment');
	}
	
	public function canDelete($objectID) {
		return WCF::getSession()->getPermission('user.toDo.attachment.canUploadAttachment');
	}
}
