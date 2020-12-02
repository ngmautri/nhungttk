<?php
namespace ApplicationTest\AssemblerTest;

use PHPUnit_Framework_TestCase;
use Procure\Domain\AccountPayable\APSnapshot;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

class AssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testCanAssemberFromArray()
    {
        echo "\n==" . __METHOD__ . "===\n";
        $target = new APSnapshot();

        $source = [
            'vendor' => 1000
        ];

        $target = GenericObjectAssembler::updateAllFieldsFromArray($target, $source);
        // var_dump($target);

        echo "\n==" . __METHOD__ . " end ===\n";
    }

    public function testCanAssemberFromObject()
    {
        echo "\n==" . __METHOD__ . "===\n";
        $target = new APSnapshot();

        $source = new APSnapshot();
        $source->vendor = 100;

        $target = GenericObjectAssembler::updateAllFieldsFrom($target, $source);
        $this->assertEquals(100, $target->vendor);
        echo "\n==" . __METHOD__ . " end ===\n";
    }

    public function testCanAssemberExcludeFromObject()
    {
        echo "\n==" . __METHOD__ . "===\n";

        $target = new APSnapshot();

        $source = new APSnapshot();
        $source->vendor = 100;
        $source->createdBy = 39;
        $source->docNumber = 'doc name';

        $fields = [
            'vendor',
            'createdBy'
        ];

        GenericObjectAssembler::updateExcludedFieldsFrom($target, $source, $fields);
        $this->assertEquals(null, $target->vendor);
        $this->assertEquals(null, $target->createdBy);
        $this->assertEquals('doc name', $target->docNumber);

        echo "\n==" . __METHOD__ . " end ===\n";
    }

    public function testCanAssemberIncludedFromObject()
    {
        echo "\n==" . __METHOD__ . "===\n";
        $target = new APSnapshot();

        $source = new APSnapshot();
        $source->vendor = 100;
        $source->createdBy = 39;
        $source->docNumber = 'doc name';

        $fields = [
            'vendor'
        ];

        $target = GenericObjectAssembler::updateIncludedFieldsFrom($target, $source, $fields);
        $this->assertEquals(100, $target->vendor);
        $this->assertEquals(null, $target->createdBy);
        $this->assertEquals(null, $target->docNumber);

        echo "\n==" . __METHOD__ . " end ===\n";
    }
}