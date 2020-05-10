<?php
namespace ProcureTest\PO\Search;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\Search\ZendSearch\PO\PoSearchIndexImpl;
use Procure\Application\Service\Search\ZendSearch\PO\PoSearchQueryImpl;
use Procure\Application\Service\Search\ZendSearch\PO\Filter\PoQueryFilter;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class RepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new POQueryRepositoryImpl($doctrineEM);

            $id = 363;
            $token = "b9753d8b-3c23-48d2-a9bb-990f41f1fe7b";

            $rootEntity = $rep->getPODetailsById($id, $token);
            // var_dump($rootEntity->makeSnapshot());

            $indexer = new PoSearchIndexImpl();
            // $r = $indexer->createDoc($rootEntity->makeSnapshot());
            // $r1 = $indexer->optimizeIndex();
            // var_dump($r1);

            $searcher = new PoSearchQueryImpl();
            $queryFilter = new PoQueryFilter();
            $queryFilter->setDocStatus("posted");
            $hits = $searcher->search("025", $queryFilter);

            $results = [];
            foreach ($hits as $hit) {
                $results[] = PORowSnapshotAssembler::createFromQueryHit($hit);
            }

            var_dump($results);

            // $string = "22-4-00078";
            // $result = preg_replace('/[0-]/', '', \substr($string, - 5)); // Replace all 'abc' with 'def'
            // echo $result;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}