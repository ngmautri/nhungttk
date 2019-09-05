<?php
namespace ProcureTest\AP;

use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Doctrine\DoctrineAPDocQueryRepository;
use Procure\Application\DTO\Ap\Output\APDocRowOutputStrategy;
use Procure\Infrastructure\Doctrine\DoctrinePRQueryRepository;

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

            $repository = new DoctrinePRQueryRepository($em);
            var_dump($repository->getPRDetailsById(912));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}