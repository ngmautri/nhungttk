<?php
namespace InventoryTest\Model;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Domain\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Inventory\Application\Service\Item\ItemCRUDService;
use Inventory\Domain\Item\ItemType;
use Inventory\Domain\Item\MonitorMethod;
use Inventory\Application\DTO\Item\ItemAssembler;

class ItemCreate extends PHPUnit_Framework_TestCase
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

            /**
             *
             * @var \Inventory\Application\Service\Item\ItemCRUDService $sv ;
             */
            $sv = Bootstrap::getServiceManager()->get('Inventory\Application\Service\Item\ItemCRUDService');

            $data = array();

            $data["itemSku"] = "2-7-5";
            $data["isActive"] = 1;
            $data["itemName"] = "Special Item 2-5 updated7";
            $data["itemDescription"] = "Special Item itemDescription";
            $data["remarks"] = "Special Item itemDescription";

            $data["standardUom"] = 1;
            $data["stockUom"] = 1;
            $data["stockUomConvertFactor"] = 2;
            $data["keywords"] = "gerber";

            $data["monitoredBy"] = MonitorMethod::ITEM_WITH_BATCH_NO;
            $dto = ItemAssembler::createItemDTOFromArray($data);

            var_dump($sv->create($dto, 1, 39, __METHOD__));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}