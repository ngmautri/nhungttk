<?php
namespace ProcureTest\GR;

use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Application\DTO\Gr\GrRowDTOAssembler;


class GrDTOAssemblerTest extends PHPUnit_Framework_TestCase
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
           $r = GrRowDTOAssembler::findMissingProperties();
           var_dump($r);
            
         } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}