<?php
namespace InventoryTest\Item;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Infrastructure\Persistence\Doctrine\ItemCategoryRepositoryImpl;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCategoryRepositoryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        /** @var EntityManager $em ; */
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $repository = new ItemCategoryRepositoryImpl($em);
        var_dump($repository->addItemToCategory(2217, 57, 39));
    }
}