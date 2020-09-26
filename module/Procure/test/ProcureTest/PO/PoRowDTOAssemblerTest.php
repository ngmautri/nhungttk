<?php
namespace ProcureTest\PR;

use Procure\Domain\Exception\InvalidArgumentException;
use Zend\Stdlib\StringWrapper\Intl;
use PHPUnit_Framework_TestCase;
use Procure\Application\DTO\Po\PORowDTOAssembler;

class PoRowDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $test = PORowDTOAssembler::createGetMapping();
            // var_dump($test);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}