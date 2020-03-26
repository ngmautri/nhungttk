<?php
namespace Application\Domain\Attachment;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AttachmentQueryRepInterface
{

    public function findAll();

    public function getById($id);
}
