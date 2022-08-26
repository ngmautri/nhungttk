<?php
namespace Application\Domain\Attachment\Validator\Contracts;

use Application\Domain\Attachment\BaseAttachment;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface AttachmentValidatorInterface
{

    public function validate(BaseAttachment $rootEntity);
}


