<?php
namespace InventoryTest\Item\Picture\CodeGenerate;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Item\Picture\AbstractPicture;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class CodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryItemPicture::class, AbstractPicture::class);
            // $result = GenericSnapshotAssembler::createAllSnapshotProps(AbstractPicture::class);
            $result = GenericObjectAssembler::printAllFields(AbstractPicture::class);
            // $result = GenericObjectAssembler::printAllFields(AbstractPicture::class, 'protected');
            // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(AbstractPicture::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}