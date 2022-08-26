<?php
namespace Application\Domain\Attachment\Repository;

use Application\Domain\Attachment\BaseAttachment;
use Application\Domain\Attachment\BaseAttachmentFile;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AttachmentCmdRepositoryInterface
{

    public function storeAttachmentHeader(BaseAttachment $rootEntity, $isPosting = false);

    public function storeWholeAttachment(BaseAttachment $rootEntity, $isPosting = false);

    public function removeAttachment(BaseAttachment $rootEntity, $isPosting = false);

    public function storeAttachmentFile(BaseAttachment $rootEntity, BaseAttachmentFile $localEntity, $isPosting = false);

    public function removeAttachmentFile(BaseAttachment $rootEntity, BaseAttachmentFile $localEntity, $isPosting = false);
}
