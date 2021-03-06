<?php
namespace InventoryTest\Model;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Domain\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Inventory\Domain\Item\ItemSnapshotAssembler;

class InventoryTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            /** @var EntityManager $em ; */
            $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            // echo Uuid::uuid4()->toString();

            $rep = new \Inventory\Infrastructure\Doctrine\DoctrineItemRepository($em);

            $item = $rep->getById(1010);

            /**
             *
             * @var \Inventory\Domain\Item\InventoryItem $newItem ;
             */
            $newItem = clone ($item);

            /**
             *
             * @var \Inventory\Domain\Item\ItemSnapshot $itemSnapShot
             */
            $itemSnapShot = $item->createItemSnapshot();
            // var_dump($itemSnapShot);

            /**
             *
             * @var \Inventory\Domain\Item\ItemSnapshot $newItemSnapShot ;
             */
            $newItemSnapShot = clone ($itemSnapShot);
            $data = array();

            $data["id"] = "2-3";

            $data["itemSku"] = "2-3";
            $data["itemName"] = "Special Item test";
            $newItemSnapShot = ItemSnapshotAssembler::updateItemSnapshotFromArray($newItemSnapShot, $data);

            var_dump($itemSnapShot->compare($newItemSnapShot));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}