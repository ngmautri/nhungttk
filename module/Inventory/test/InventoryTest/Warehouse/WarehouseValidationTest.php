<?php
namespace InventoryTest\Warehouse;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
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
class WarehouseValidationAssemblerTest extends PHPUnit_Framework_TestCase
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

        $data = array();
        $data["whName"] = "NMT";
        $data["whCode"] = "WH12";
        $data["company"] = 1;
        $data["createdBy"] = 39;
     
        // create new transaction.
        $dto = WarehouseDTOAssembler::createDTOFromArray($data);
        // var_dump($dto);

        $snapshot = WarehouseSnapshotAssembler::createSnapshotFromArray($data);
        
        $wh = new GenericWarehouse();
        $repository = new DoctrineWarehouseCmdRepository($em);
        $wh->setCmdRepository($repository);
        
        $repository = new DoctrineWarehouseQueryRepository($em);
        $wh->setQueryRepository($repository);
        $sharedSpecificationFactory = new ZendSpecificationFactory($em);
        $wh->setSharedSpecificationFactory($sharedSpecificationFactory);
        
        $wh->makeFromSnapshot($snapshot);
        var_dump($wh->validate());
        
      
        
        
    }
}