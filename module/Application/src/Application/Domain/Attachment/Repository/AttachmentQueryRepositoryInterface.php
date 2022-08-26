<?php
namespace Application\Domain\Attachment\Repository;

use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AttachmentQueryRepositoryInterface
{

    public function getRootByMemberId($memberId);

    public function getById($id);

    public function getByUUID($uuid);

    public function getList(CompanySqlFilterInterface $filter);
}
