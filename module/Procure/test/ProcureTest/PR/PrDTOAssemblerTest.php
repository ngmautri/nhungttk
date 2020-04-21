<?php
namespace ProcureTest\PR;

use Procure\Application\DTO\Pr\PrDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class PrDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            PrDTOAssembler::createStoreMapping();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}