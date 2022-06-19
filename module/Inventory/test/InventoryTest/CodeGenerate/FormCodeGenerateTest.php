<?php
namespace InventoryTest\Department\CodeGenerate;

use Application\Application\Contracts\GenericDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\SQL\Filter\PrHeaderReportSqlFilter;
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
                "resultPerPage",
                "itemId",
                "vendorId",
                "invoiceId",
                "renderType"
            ];

            // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(ItemSerialSqlFilter::class);
            // $result = GenericDTOAssembler::createFormElementsFor(ItemSerialSqlFilter::class, $properties);
            $result = GenericDTOAssembler::printFieldsAsRequestString(PrHeaderReportSqlFilter::class);
            // $result = GenericDTOAssembler::createFormElementsFunctionFor(ItemSerialSqlFilter::class, $properties);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}