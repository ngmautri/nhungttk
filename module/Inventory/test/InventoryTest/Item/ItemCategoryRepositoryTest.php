<?php
namespace InventoryTest\Item;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseQueryRepository;
use PHPUnit_Framework_TestCase;
use Inventory\Infrastructure\Persistence\Doctrine\ItemCategoryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCategoryRepositoryTest extends PHPUnit_Framework_TestCase
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
        /** @var EntityManager $em ; */
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $repository = new ItemCategoryRepositoryImpl($em);
        var_dump($repository->getItemsByCategory(6)[3]);      
    }
}