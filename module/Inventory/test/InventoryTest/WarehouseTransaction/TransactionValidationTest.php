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
use Inventory\Infrastructure\Doctrine\DoctrineTransactionRepository;
use Inventory\Domain\Service\TransactionSpecificationService;

class TransactionValidationAssemblerTest extends PHPUnit_Framework_TestCase
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
        $data["movementDate"] = "2019-07-14";
        $data["movementType"] = TransactionType::GI_FOR_REPAIR_MACHINE_WITH_EX;
        $data["warehouse"] = 5;
        $data["company"] = 1;
        $data["currency"] = 1;
        $data["docCurrency"] = 1;
        $data["localCurrency"] = 1;
        $data["createdBy"] = 46;

        // create new transaction.
        $dto = TransactionDTOAssembler::createDTOFromArray($data);
        // var_dump($dto);

        $snapshot = TransactionSnapshotAssembler::createSnapshotFromArray($data);

        $trx = TransactionFactory::createTransaction(TransactionType::GI_FOR_REPAIR_MACHINE_WITH_EX);

        $trx->makeFromSnapshot($snapshot);

        $data = array();

        /*
         * $data["item"] = 1010;
         * $data["docQuantity"] = 1;
         * $data["costCenter"] = 4;
         * $rowSnapshot = TransactionRowSnapshotAssembler::createSnapshotFromArray($data);
         */

        // $trx->addRowFromSnapshot($rowSnapshot);

        $data["item"] = 2427;
        $data["docQuantity"] = 4;
        $data["costCenter"] = 5;
        $rowSnapshot = TransactionRowSnapshotAssembler::createSnapshotFromArray($data);

        $domainSpecificationFactory = new DoctrineSpecificationFactory($em);
        $sharedSpecificationFactory = new ZendSpecificationFactory($em);
        $specService = new TransactionSpecificationService($sharedSpecificationFactory, $domainSpecificationFactory);

        $trx->addRowFromSnapshot($specService, $rowSnapshot);

        $em->getConnection()->beginTransaction(); // suspend auto-commit
        try {

            var_dump($trx->validate($specService));
            // var_dump($trx->post());
        } catch (\Exception $e) {
            echo $e->getMessage();
            $em->getConnection()->rollBack();
        }
    }
}