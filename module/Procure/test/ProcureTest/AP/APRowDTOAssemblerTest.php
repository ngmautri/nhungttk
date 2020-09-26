<?php
namespace ProcureTest\AP;

use Procure\Application\DTO\Ap\ApRowDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class APRowDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            ApRowDTOAssembler::createGetMapping();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}