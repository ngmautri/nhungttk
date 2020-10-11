<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Notification;
use Application\Domain\Attachment\AbstractAttachment;
use Application\Domain\Service\AttachmentPosingService;
use Application\Domain\Service\AttachmentSpecService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericAttachment extends AbstractAttachment
{

    abstract protected function prePost(AttachmentSpecService $specService, AttachmentPosingService $postingService, Notification $notification = null);

    abstract protected function doPost(AttachmentSpecService $specService, AttachmentPosingService $postingService, Notification $notification = null);

    abstract protected function afterPost(AttachmentSpecService $specService, AttachmentPosingService $postingService, Notification $notification = null);

    abstract protected function specificHeaderValidation(AttachmentSpecService $specService, Notification $notification, $isPosting = false);
}