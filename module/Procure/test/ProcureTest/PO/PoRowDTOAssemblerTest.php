<?php
namespace ProcureTest\PR;

use Procure\Application\DTO\Pr\PrDTOAssembler;

use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Application\DTO\Pr\PrRowDTOAssembler;
use Procure\Application\DTO\Po\PORowDTOAssembler;

class PoRowDTOAssemblerTest extends PHPUnit_Framework_TestCase
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

            PORowDTOAssembler::createGetMapping();
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}