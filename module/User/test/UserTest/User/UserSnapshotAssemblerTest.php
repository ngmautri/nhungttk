<?php
namespace UserTest\User;

use Procure\Domain\Exception\InvalidArgumentException;
use User\Domain\User\BaseUser;
use PHPUnit_Framework_TestCase;

class UserSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = UserSnapshotAssembler::c();
            BaseUser::createSnapshotBaseProps();
            // var_dump($result);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}