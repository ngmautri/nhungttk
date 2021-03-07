<?php
namespace InventoryTest\Item\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Entity\NmtInventoryItem;
use Inventory\Domain\Item\AbstractItem;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\GenericItemSnapshot;
use Application\Entity\HrIndividual;
use HR\Domain\Employee\AbstractIndividual;
use HR\Domain\Employee\BaseEmployee;
use HR\Domain\Employee\BaseIndividual;
use HR\Domain\Employee\BaseIndividualSnapshot;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(HrIndividual::class, AbstractIndividual::class);
            // $result = GenericSnapshotAssembler::findMissingProps(GenericItem::class, GenericItemSnapshot::class);

            // $result = GenericSnapshotAssembler::createSnapshotProps(BaseEmployee::class, );

            // $result = GenericSnapshotAssembler::createAllSnapshotProps(UomPair::class);
            $result = GenericSnapshotAssembler::createSnapshotProps(BaseIndividual::class, AbstractIndividual::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}