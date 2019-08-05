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
use Inventory\Domain\Warehouse\Location\DefaultLocation;
use Inventory\Application\DTO\Warehouse\Location\LocationDTOAssembler;
use Inventory\Domain\Warehouse\Location\LocationSnapshotAssembler;
use Inventory\Domain\Warehouse\Location\GenericLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ValidationAssemblerTest extends PHPUnit_Framework_TestCase
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
        
        /** @var EntityManager $em ; */
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        
        $repository = new DoctrineWarehouseQueryRepository($em);
        $wh = $repository->getById(13);
        
        $repository = new DoctrineWarehouseCmdRepository($em);
        $wh->setCmdRepository($repository);
        
        $repository = new DoctrineWarehouseQueryRepository($em);
        $wh->setQueryRepository($repository);
   
        $sharedSpecificationFactory = new ZendSpecificationFactory($em);
        $wh->setSharedSpecificationFactory($sharedSpecificationFactory);
        
        //var_dump($wh->makeDTO());
        
        
        $data = array();
        //
        $data["locationName"] = DefaultLocation::ROOT_LOCATION;
        $data["locationName"] = "asddsaf";
        $data["createdBy"] = 39;
        $data["parentId"] = 47;
        
        // create new transaction.
        $dto = LocationDTOAssembler::createDTOFromArray($data);
        // var_dump($dto);

        $snapshot = LocationSnapshotAssembler::createSnapshotFromArray($data);
        $location = new GenericLocation();
        $location->makeFromSnapshot($snapshot);
        var_dump($wh->validateLocation($location));
     
        
      
        
        
    }
}