<?php
namespace ApplicationTest\Brand\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Entity\NmtApplicationBrand;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(NmtApplicationBrand::class, AbstractBrand::class);
            // $result = GenericSnapshotAssembler::createSnapshotProps(GenericAttribute::class, BaseAttribute::class);
            // $result = GenericObjectAssembler::printAllFields(AbstractBrand::class, 'private');
            $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(NmtApplicationBrand::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}