<?php
namespace ProcureTest\PR;

use Procure\Application\DTO\Pr\PrDTOAssembler;

use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class PrDTOAssemblerTest extends PHPUnit_Framework_TestCase
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

            PrDTOAssembler::createDTOProperities();
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}