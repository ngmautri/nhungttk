<?php
namespace ProcureTest\AP;

use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\DoctrineAPInvoiceQueryRepository;
use PHPUnit_Framework_TestCase;
use Procure\Application\DTO\Ap\Output\APInvoiceRowOutputStrategy;


class APInvoiceQueryRepository extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;
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
            
            $repository = new DoctrineAPInvoiceQueryRepository($em);
            var_dump($repository->getById(2034, APInvoiceRowOutputStrategy::OUTPUT_IN_ARRAY)->getRowsOutput());      

           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}