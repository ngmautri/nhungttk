<?php
namespace InventoryTest\WarehouseTransaction;

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
use Inventory\Application\Service\Warehouse\TransactionService;

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
        $data["movementType"] = TransactionType::GI_FOR_COST_CENTER;
        $data["warehouse"] = 6;
        $data["company"] = 1;
        $data["docCurrency"] = 9;
        $data["localCurrency"] = 1;

        $dto= TransactionDTOAssembler::createDTOFromArray($data);
        /**
         *
         * @var \Inventory\Application\Service\Warehouse\TransactionService $sv ;
         */
        $sv = Bootstrap::getServiceManager()->get('Inventory\Application\Service\Warehouse\TransactionService');
        
        var_dump($sv->getHeader(689));
        
        //var_dump(TransactionFactory::getGoodIssueTransactions());
        
    }
}