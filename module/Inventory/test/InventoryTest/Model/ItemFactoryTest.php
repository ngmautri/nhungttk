<?php
namespace InventoryTest\Model;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Domain\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Inventory\Domain\Item\ItemSnapshotAssembler;
use Inventory\Domain\Item\Factory\ItemFactory;
use Inventory\Domain\Item\ItemType;
use Application\Application\Specification\Zend\ZendSpecificationFactory;

class ItemFactoryTest extends PHPUnit_Framework_TestCase
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

            $data = array();

            $data["company"] = 1;
            $data["createdBy"] = 39;
            $data["itemName"] = "Special Item";
            $data["itemSku"] = "3-5";
            $data["standardUom"] = 1;
            $data["stockUom"] = 1;
            $data["stockUomConvertFactor"] = 1;

            $snapshot = ItemSnapshotAssembler::createSnapshotFromArray($data);

            $item = ItemFactory::createItem(ItemType::INVENTORY_ITEM_TYPE);
            $item->makeFromSnapshot($snapshot);
            $sharedSpecificationFactory = new ZendSpecificationFactory($em);
            $item->setSharedSpecificationFactory($sharedSpecificationFactory);

            var_dump($item->validate());

            // var_dump($item->createSnapshot());
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}