<?php
namespace ProcureTest\PR;

use Procure\Application\DTO\Pr\PrDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class PrDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            PrDTOAssembler::createGetMapping();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}