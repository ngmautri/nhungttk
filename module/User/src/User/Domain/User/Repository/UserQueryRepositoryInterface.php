<?php
namespace User\Domain\User\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface UserQueryRepositoryInterface
{

    public function getByTokenId($id, $token);

    public function getById($id);

    public function getVersion($id, $token = null);
}
