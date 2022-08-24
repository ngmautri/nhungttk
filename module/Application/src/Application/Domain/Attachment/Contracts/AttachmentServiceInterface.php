<?php
namespace Application\Domain\Attachment\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AttachmentServiceInterface
{

    function createAttachmentFileFrom($path);
}