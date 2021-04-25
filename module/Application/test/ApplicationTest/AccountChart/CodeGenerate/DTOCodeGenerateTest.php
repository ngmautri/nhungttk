<?php
namespace ApplicationTest\Department\CodeGenerate;

use Application\Domain\Company\AccountChart\AccountSnapshot;
use Application\Domain\Company\AccountChart\AccountSnapshotAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = ChartSnapshotAssembler::createFormElementsFor(ChartSnapshot::class);
            $result = AccountSnapshotAssembler::createFormElementsFunctionFor(AccountSnapshot::class);
            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}