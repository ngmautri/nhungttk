<?php
namespace User\Domain\User;

use Application\Domain\Shared\SnapshotAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericUser extends BaseUser
{

    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new UserSnapshot());
    }
}