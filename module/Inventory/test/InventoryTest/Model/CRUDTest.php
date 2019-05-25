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

class CRUDTest extends PHPUnit_Framework_TestCase
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
            $eventManager = Bootstrap::getServiceManager()->get('EventManager');
            
            
            /**
             * 
             * @var \Inventory\Application\Service\Item\ItemCRUDService $sv ;
             */
            $sv = Bootstrap::getServiceManager()->get('Inventory\Application\Service\Item\ItemCRUDService');
            
            $data = array();
            
            $data["itemSku"]="2-3";
            $data["isActive"]=1;
            $data["itemName"]="Special Item";
            $data["standardUom"]=1;
            $data["itemTypeId"]=ItemType::INVENTORY_ITEM_TYPE;
            
            $itemAssembler = new \Inventory\Application\DTO\Item\ItemAssembler();
            $dto = $itemAssembler->createItemDTOFromArray($data);
            
            
            /* $service = new ItemCRUDService();
            $service->setEventManager($eventManager);
            $service->setDoctrineEM($em);
             */
            
            var_dump($notificattion = $sv->save($dto, 39));
            
            
            
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}