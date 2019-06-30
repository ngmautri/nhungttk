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
use Inventory\Infrastructure\Doctrine\DoctrineTransactionRepository;
use Inventory\Application\DTO\Warehouse\Transaction\Output\TransactionRowOutputStrategy;

class TransactionRepositoryTest extends PHPUnit_Framework_TestCase
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
        $rep = new DoctrineTransactionRepository($em);
        $trx = $rep->getById(677, TransactionRowOutputStrategy::OUTPUT_IN_ARRAY);        
        var_dump($trx->getTranstionRowsOutput());
        
        $domainSpecificationFactory = new DoctrineSpecificationFactory($em);
        $trx->setDomainSpecificationFactory($domainSpecificationFactory);
        
        $sharedSpecificationFactory = new ZendSpecificationFactory($em);
        $trx->setSharedSpecificationFactory($sharedSpecificationFactory);
       
        //var_dump($trx->validate());
        
    }
}