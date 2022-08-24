<?php
namespace Application\Domain\Company\Brand\Validator\Contracts;

use Application\Domain\Attachment\GenericAttachment;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface AttachmentValidatorInterface
{

    public function validate(GenericAttachment $rootEntity);
}


