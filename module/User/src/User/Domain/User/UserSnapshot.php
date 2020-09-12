<?php
namespace User\Domain\User;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserSnapshot extends BaseUserSnapshot
{

    public $roleList;

    /**
     *
     * @return mixed
     */
    public function getRoleList()
    {
        return $this->roleList;
    }
}