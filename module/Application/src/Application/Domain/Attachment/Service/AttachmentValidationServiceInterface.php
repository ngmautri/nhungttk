<?php
namespace Application\Domain\Attachment\Service;

use Application\Domain\Attachment\Contracts\AttachmentFileValidatorInterface;
use Application\Domain\Attachment\Validator\Contracts\AttachmentValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface AttachmentValidationServiceInterface
{

    /**
     *
     * @return AttachmentValidatorInterface ;
     */
    public function getAttachmentValidators();

    /**
     *
     * @return AttachmentFileValidatorInterface ;
     */
    public function getAttachmentFileValidators();
}
