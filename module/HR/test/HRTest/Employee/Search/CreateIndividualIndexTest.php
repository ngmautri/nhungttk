<?php
namespace HRTest\Item\Search;

use Application\Domain\Shared\Person\Gender;
use Doctrine\ORM\EntityManager;
use HR\Application\Service\Search\ZendSearch\Individual\IndividualSearchIndexImpl;
use HR\Domain\Employee\IndividualSnapshot;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class CreateIndividualIndexTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $stopWatch = new Stopwatch();
            $indexer = new IndividualSearchIndexImpl($doctrineEM);

            $snapshot = new IndividualSnapshot();
            $snapshot->id = 1950;
            $snapshot->firstName = "Nguyen";
            $snapshot->lastName = "Mau Tri";
            $snapshot->employeeCode = "0651";
            $snapshot->gender = Gender::MALE;

            $timer = $stopWatch->start("test");
            $r = $indexer->createDoc($snapshot);
            var_dump($r);
            $r = $indexer->optimizeIndex();
            var_dump($r);

            $timer = $stopWatch->stop("test");
            echo $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}