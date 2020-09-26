<?php
namespace ProcureTest\PR;

use Doctrine\ORM\EntityManager;
use Procure\Application\DTO\Po\PoDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class PoDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // var_dump(Inflector::singularize('cakes'));

            PoDTOAssembler::createGetMapping();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}