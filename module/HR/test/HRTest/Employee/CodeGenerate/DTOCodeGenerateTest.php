<?php
namespace HRTest\Emnployee\CodeGenerate;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Entity\NmtInventoryItem;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Application\Entity\HrIndividual;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = GenericDTOAssembler::createGetMapping(HrIndividual::class);
            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}