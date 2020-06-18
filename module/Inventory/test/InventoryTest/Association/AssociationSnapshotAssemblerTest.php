<?php
namespace InventoryTest\Item;

use Inventory\Domain\Association\BaseAssociation;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class AssociationSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = AssociationSnapshotAssembler::findMissingPropsInBaseObject();
            BaseAssociation::createSnapshotProps();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}