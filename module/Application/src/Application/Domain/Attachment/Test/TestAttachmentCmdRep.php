<?php
namespace Application\Domain\Attachment\Test;

use Application\Domain\Attachment\BaseAttachment;
use Application\Domain\Attachment\BaseAttachmentFile;
use Application\Domain\Attachment\Repository\AttachmentCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TestAttachmentCmdRep implements AttachmentCmdRepositoryInterface
{

    public function storeWholeAttachment(BaseAttachment $rootEntity, $isPosting = false)
    {
        echo __METHOD__;
    }

    public function removeAttachmentFile(BaseAttachment $rootEntity, BaseAttachmentFile $localEntity, $isPosting = false)
    {
        echo __METHOD__;
    }

    public function storeAttachmentFile(BaseAttachment $rootEntity, BaseAttachmentFile $localEntity, $isPosting = false)
    {
        echo __METHOD__;
    }

    public function removeAttachment(BaseAttachment $rootEntity, $isPosting = false)
    {
        echo __METHOD__;
    }

    public function storeAttachmentHeader(BaseAttachment $rootEntity, $isPosting = false)
    {
        return $rootEntity->makeSnapshot();
    }
}
