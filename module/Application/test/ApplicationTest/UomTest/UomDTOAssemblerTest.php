<?php
namespace ApplicationTest\UomTest;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Entity\NmtApplicationUom;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Application\Entity\AppUomGroup;
use Application\Entity\AppUomGroupMember;

class UomDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = GenericDTOAssembler::createGetMapping(NmtApplicationUom::class);
            \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}