<?php
namespace InventoryTest\ComponentItem;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseServiceTest extends PHPUnit_Framework_TestCase
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
        $data["whName"] = "Supplies for LOG WH";
        $data["whCode"] = "WH8";
        $data["whAddress"] = "VITA Park Specific Economic Zone Km 22, Ban Nonthong, Saythany District, Vientiane Capital";
        $data["company"] = 1;
        $data["whCountry"] = 2;
        $data["createdBy"] = 39;

        // create new transaction.
        $dto = WarehouseDTOAssembler::createDTOFromArray($data);

        var_dump($sv->updateHeader(12, "", $dto, 1, ""));
    }
}