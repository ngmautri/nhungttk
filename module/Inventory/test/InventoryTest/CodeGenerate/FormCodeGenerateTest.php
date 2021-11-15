<?php
namespace InventoryTest\Department\CodeGenerate;

use Application\Application\Contracts\GenericDTOAssembler;
use Inventory\Infrastructure\Persistence\SQL\Filter\ItemSerialSqlFilter;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class FormCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $properties = [

                "docYear",
                "docMonth",
                "isActive",
                "resultPerPage"
            ];

            // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(ItemSerialSqlFilter::class);
            $result = GenericDTOAssembler::createFormElementsFor(ItemSerialSqlFilter::class, $properties);
            // $result = GenericDTOAssembler::createFormElementsFunctionFor(ItemSerialSqlFilter::class, $properties);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}