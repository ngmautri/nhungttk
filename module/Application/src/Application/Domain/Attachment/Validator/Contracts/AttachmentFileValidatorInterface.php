<?php
namespace Application\Domain\Attachment\Contracts;

use Application\Domain\Attachment\BaseAttachment;
use Application\Domain\Attachment\BaseAttachmentFile;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface AttachmentFileValidatorInterface
{

    public function validate(BaseAttachment $rootEntity, BaseAttachmentFile $localEntity);
}

