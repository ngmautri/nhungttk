<?php
namespace InventoryTest\Warehouse;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTOAssembler;
use Inventory\Application\Specification\Doctrine\DoctrineSpecificationFactory;
use Inventory\Domain\Warehouse\Transaction\Factory\TransactionFactory;
use Inventory\Domain\Warehouse\Transaction\TransactionRowSnapshotAssembler;
use Inventory\Domain\Warehouse\Transaction\TransactionSnapshotAssembler;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use PHPUnit_Framework_TestCase;
use Inventory\Infrastructure\Doctrine\DoctrineTransactionRepository;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use Inventory\Domain\Warehouse\WarehouseSnapshotAssembler;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseRepository;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseCmdRepository;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseQueryRepository;

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
        $data["whCode"] = "WH1";
        $data["company"] = 1;
        $data["createdBy"] = 390;
     
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