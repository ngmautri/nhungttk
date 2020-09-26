<?php
namespace ProcureTest\PR;

use Doctrine\ORM\EntityManager;
use Procure\Application\DTO\Pr\PrRowDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class PrRowDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            PrRowDTOAssembler::createGetMapping();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}