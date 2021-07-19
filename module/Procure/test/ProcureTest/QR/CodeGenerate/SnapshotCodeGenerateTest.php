<?php
namespace ProcureTest\QR\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Entity\NmtProcureQoRow;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(NmtProcureQoRow::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}