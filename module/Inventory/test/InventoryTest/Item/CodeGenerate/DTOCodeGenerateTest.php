<?php
namespace InventoryTest\Item\CodeGenerate;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Entity\AppUomGroup;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Application\Entity\AppUomGroupMember;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = GenericDTOAssembler::createGetMapping(AppUomGroupMember::class);
            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}