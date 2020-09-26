<?php
namespace ProcureTest\GR;

use Procure\Domain\Exception\InvalidArgumentException;
use Zend\Stdlib\StringWrapper\Intl;
use PHPUnit_Framework_TestCase;
use Procure\Application\DTO\Gr\GrDTOAssembler;

class GrDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            GrDTOAssembler::createGetMapping();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}