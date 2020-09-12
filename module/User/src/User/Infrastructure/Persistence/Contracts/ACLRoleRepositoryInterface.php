<?php
namespace User\Infrastructure\Persistence\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface ACLRoleRepositoryInterface
{

    public function getRole($roleId);

    public function getRoot();
}
