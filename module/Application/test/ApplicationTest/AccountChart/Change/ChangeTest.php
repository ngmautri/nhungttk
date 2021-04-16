<?php
namespace InventoryTest\Item\Change;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Domain\Item\Contracts\ItemType;
use PHPUnit_Framework_TestCase;

class ChangeTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        $file = $root . "/InventoryTest/Data/itemId.php";

        /** @var EntityManager $doctrineEM ; */
        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $items = include $file;
        foreach ($items as $id) {
            $criteria = array(
                'id' => $id
            );

            /**
             *
             * @var \Application\Entity\NmtInventoryItem $entity ;
             */

            $entity = $doctrineEM->getRepository('\Application\Entity\NmtInventoryItem')->findOneBy($criteria);
            $entity->setItemTypeId(ItemType::SERVICE_ITEM_TYPE);
            $entity->setIsFixedAsset(0);
            $entity->setIsSparepart(0);
            $entity->setIsStocked(0);
            $entity->setStockUomConvertFactor(null);
            $entity->setPurchaseUom($entity->getStandardUom());
            $entity->setPurchaseUomConvertFactor(1);
            $doctrineEM->persist($entity);
        }

        $doctrineEM->flush();
    }
}