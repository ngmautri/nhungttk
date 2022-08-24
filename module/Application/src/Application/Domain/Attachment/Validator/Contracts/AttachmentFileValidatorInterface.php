<?php
namespace Application\Domain\Attachment\Contracts;

use Application\Domain\Attachment\GenericAttachment;
use Application\Domain\Attachment\GenericAttachmentFile;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface AttachmentFileValidatorInterface
{

    public function validate(GenericAttachment $rootEntity, GenericAttachmentFile $localEntity);
}

