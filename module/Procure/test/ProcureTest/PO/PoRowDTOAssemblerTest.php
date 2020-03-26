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
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            $test = PORowDTOAssembler::findMissingProperties();
            var_dump($test);
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}