<?php
namespace ProcureTest\Model;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Model\Domain\PurchaseRequest\PurchaseRequest;
use Procure\Domain\APInvoice\Repository\Doctrine\DoctrineAPInvoiceRepository;
use Procure\Infrastructure\Persistence\DoctrinePRListRepository;


class PrListRespository extends PHPUnit_Framework_TestCase
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
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            /** @var EntityManager $em ; */
            $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            //echo Uuid::uuid4()->toString();
            
            $rep = new DoctrinePRListRepository();
            $rep->setDoctrineEM($em);
            
            $result = $rep->getPrStatus(835, 1);
            var_dump(count($result));

           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}