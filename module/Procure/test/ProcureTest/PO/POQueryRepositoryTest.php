<?php
namespace ProcureTest\PO;

use ProcureTest\Bootstrap;
use Procure\Application\Reporting\PR\PrRowStatusReporter;
use Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\DoctrinePRListRepository;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;

class POQueryRepositoryTest extends PHPUnit_Framework_TestCase
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
            $rep = new DoctrinePOQueryRepository($doctrineEM);
                        
            $result = $rep->getFullDetailsById(199, null);
            var_dump($result);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}