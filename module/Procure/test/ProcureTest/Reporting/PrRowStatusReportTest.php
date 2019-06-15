<?php
namespace ProcureTest\DTO;

use ProcureTest\Bootstrap;
use Procure\Application\Reporting\PR\PrRowStatusReporter;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\DoctrinePRListRepository;
use PHPUnit_Framework_TestCase;
use Doctrine\Entity\E;
use Doctrine\ORM\EntityManager;

class PrRowStatusReportTest extends PHPUnit_Framework_TestCase
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
            $rep = new DoctrinePRListRepository();
            $rep->setDoctrineEM($doctrineEM);

            $reporter = new PrRowStatusReporter();
            $reporter->setPrListRespository($rep);
            $reporter->setDoctrineEM($doctrineEM);
            $results = $reporter->getPrRowStatus(1, 2019, 1, "itemName", "ASC",0,0);

            $n = 0;
            foreach ($results as $result) {
                var_dump((array) $result);
                $n ++;
                echo $n;
                if ($n == 1) {
                    break;
                }
            }
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}