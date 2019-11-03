<?php
namespace ProcureTest\PR;

use Doctrine\ORM\EntityManager;
use Procure\Application\DTO\Pr\PrDTOAssembler;

use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Application\DTO\Po\PoDTOAssembler;
use Symfony\Component\Inflector\Inflector;

class PoDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
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
            
            //var_dump(Inflector::singularize('cakes'));

            PoDTOAssembler::createDTOProperities();
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}