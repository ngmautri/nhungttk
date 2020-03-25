<?php
namespace ProcureTest\PO;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Reporting\PR\PrRowStatusReporter;
use Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\DoctrinePRListRepository;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;

class GRQueryRepositoryTest extends PHPUnit_Framework_TestCase
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
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $rep = new GRQueryRepositoryImpl($doctrineEM);                        
            $result = $rep->getHeaderById(72);
            var_dump($result);
           
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}