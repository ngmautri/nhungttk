<?php
namespace InventoryTest\WarehouseTransaction;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTOAssembler;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use PHPUnit_Framework_TestCase;

class TransactionServiceTest extends PHPUnit_Framework_TestCase
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
        $data["movementDate"] = "2019-06-14";
        $data["movementType"] = TransactionType::GI_FOR_REPAIR_MACHINE_WITH_EX;
        $data["warehouse"] = 6;
        $data["company"] = 1;
        $data["docCurrency"] = 9;
        $data["localCurrency"] = 1;

        $dto = TransactionDTOAssembler::createDTOFromArray($data);
        /**
         *
         * @var \Inventory\Application\Service\Warehouse\TransactionService $sv ;
         */
        $sv = Bootstrap::getServiceManager()->get('Inventory\Application\Service\Warehouse\TransactionService');

        // var_dump($sv->getHeader(689));

        var_dump($sv->post(699, null, null));

        // var_dump(TransactionFactory::getGoodIssueTransactions());
    }
}