<?php
namespace ProcureTest\AP;

use Doctrine\ORM\EntityManager;
use Procure\Application\DTO\Ap\ApDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ApDTOAssemblerTest extends PHPUnit_Framework_TestCase
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

            ApDTOAssembler::createStoreMapping();

           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}