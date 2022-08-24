<?php
namespace Application\Domain\Attachment\Service;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface AttachmentValidationServiceInterface
{

    public function getAttachmentValidators();

    public function getAttachmentFileValidators();
}
