<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Notification;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Domain\APInvoice\Factory\APFactory;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecificationService;
use Application\Domain\Shared\DTOFactory;
use Procure\Domain\Exception\InvalidArgumentException;
use Application\Domain\Attachment\AbstractAttachment;
use Application\Domain\Service\AttachmentSpecService;
use Application\Domain\Service\AttachmentPosingService;

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