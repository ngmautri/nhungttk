<?php
namespace ApplicationTest\CompanyTest\CodeGenerate;

use Application\Domain\Company\CompanySnapshot;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Entity\NmtApplicationCompany;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class CodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericObjectAssembler::findMissingProps(NmtApplicationCompany::class, CompanySnapshot::class);

            GenericObjectAssembler::createStoreMapping(NmtApplicationCompany::class);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}