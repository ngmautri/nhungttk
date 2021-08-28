<?php
namespace InventoryTest\ComponentItem;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\WarehouseSnapshotAssembler;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseCmdRepository;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseQueryRepository;
use PHPUnit_Framework_TestCase;
use Inventory\Application\Service\Warehouse\WarehouseService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseCreateServiceTest extends PHPUnit_Framework_TestCase
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

        /**
         *
         * @var WarehouseService $sv
         */
        $sv = Bootstrap::getServiceManager()->get('Inventory\Application\Service\Warehouse\WarehouseService');

        $data = array();
        $data["whName"] = "NMT";
        $data["whCode"] = "WH12";
        $data["company"] = 1;
        $data["whCountry"] = 2;
        $data["createdBy"] = 39;

        // create new transaction.
        $dto = WarehouseDTOAssembler::createDTOFromArray($data);
        // var_dump($dto);

        var_dump($sv->createHeader($dto, 1, 39, ""));
    }
}