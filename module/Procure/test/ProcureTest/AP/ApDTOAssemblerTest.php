<?php
namespace ProcureTest\AP;

use Procure\Application\DTO\Ap\ApDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ApDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            ApDTOAssembler::createGetMapping();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}