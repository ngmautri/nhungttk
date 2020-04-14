<?php
namespace ProcureTest\AP;

use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Application\DTO\Ap\APDocDTOAssembler;

class APDocDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            APDocDTOAssembler::createDTOProperities();

           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}