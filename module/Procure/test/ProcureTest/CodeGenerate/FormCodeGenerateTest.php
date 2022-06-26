<?php
namespace ProcureTest\PR\CodeGenerate;

use Application\Application\Contracts\GenericDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Domain\PurchaseRequest\Definition\PrDefinition;
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

            // $result = GenericDTOAssembler::createFormElementsFor(ItemSerialSqlFilter::class, $properties);
            // $result = GenericDTOAssembler::createFormElements(PrHeaderReportSqlFilter::class);
            // $result = GenericDTOAssembler::createFormElementsFunction(PrHeaderReportSqlFilter::class);
            // $result = GenericDTOAssembler::createFormElementsFor(PRSnapshot::class, PrDefinition::$defaultIncludedFields);
            $result = GenericDTOAssembler::createFormElementsFunctionFor(PRSnapshot::class, PrDefinition::$defaultIncludedFields);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}