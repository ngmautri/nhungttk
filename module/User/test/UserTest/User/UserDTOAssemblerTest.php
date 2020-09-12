<?php
namespace UserTest\User;

use User\Application\DTO\User\UserDTOAssembler;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class UserDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = UserDTOAssembler::createGetMapping();
            var_dump($result);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}