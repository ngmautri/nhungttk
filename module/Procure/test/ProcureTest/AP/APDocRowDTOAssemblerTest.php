<?php
namespace ProcureTest\AP;

use Procure\Application\DTO\Ap\APDocRowDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class APDocRowDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

  
    protected $em;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            APDocRowDTOAssembler::createGetMapping();

           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}