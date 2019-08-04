<?php
namespace InventoryTest\Warehouse;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\WarehouseSnapshotAssembler;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseCmdRepository;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseQueryRepository;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseQueryRepositoryAssemblerTest extends PHPUnit_Framework_TestCase
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

        $repository = new DoctrineWarehouseQueryRepository($em);
        var_dump($repository->getById(5));      
        
        
    }
}