<?php
namespace User\Domain\User\Factory;

use Application\Domain\Shared\SnapshotAssembler;
use User\Domain\User\GenericUser;
use User\Domain\User\UserSnapshot;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserFactory
{

    public static function contructFromDB($snapshot)
    {
        if (! $snapshot instanceof UserSnapshot) {
            throw new InvalidArgumentException("Usersnapshot not found!");
        }

        $instance = new GenericUser();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }
}