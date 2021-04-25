<?php
namespace ApplicationTest\Department\CodeGenerate;

use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\Department\DepartmentSnapshotAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = DepartmentSnapshotAssembler::createFormElementsFor(DepartmentSnapshot::class);
            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}