<?php
namespace ProcureTest\AP;

use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Doctrine\DoctrineAPDocQueryRepository;
use Procure\Application\DTO\Ap\Output\APDocRowOutputStrategy;

class APInvoiceQueryRepository extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    protected $em;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            /** @var EntityManager $em ; */
            $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $repository = new DoctrineAPDocQueryRepository($em);
            var_dump($repository->getById(2054, APDocRowOutputStrategy::OUTPUT_IN_ARRAY)->getRowsOutput());
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}