<?php
namespace ProcureTest\PO\Search;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchQueryImpl;
use Inventory\Application\Service\Search\ZendSearch\Item\Filter\ItemQueryFilter;
use Inventory\Domain\Item\ItemSnapshotAssembler;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
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

            // $r = $indexer->createDoc($rootEntity->makeSnapshot());
            // $r1 = $indexer->optimizeIndex();
            // var_dump($r1);

            $searcher = new ItemSearchQueryImpl();
            $queryFilter = new ItemQueryFilter();

            // $hits = $searcher->search("820003898", $queryFilter);

            // $hits = $searcher->search("820003898", $queryFilter);

            // $hits = $searcher->search("3588", $queryFilter);

            // $hits = $searcher->search("40059*", $queryFilter);

            $results = $searcher->search("2-5", $queryFilter);
            echo $results->getMessage() . "\n";

            $results_array = [];

            $results_array['message'] = $results->getMessage();
            $results_array['total_hits'] = $results->getTotalHits();

            $hits_array = [];
            $n = 0;

            foreach ($results->getHits() as $hit) {
                $n ++;
                $item_thumbnail = '/images/no-pic1.jpg';
                if ($hit->item_id != null) {
                    $item_thumbnail = "test";
                }

                $hits_array["item_thumbnail"] = $item_thumbnail;

                $hits_array["n"] = $n;
                $hits_array["value"] = \sprintf('%s | %s', $hit->itemSku, $hit->itemName);
                $hits_array["hit"] = ItemSnapshotAssembler::createFromQueryHit($hit);
            }

            $results_array['hits'] = $hits_array;
            var_dump(\json_encode($results_array));

            // $string = "22-4-00078";
            // $result = preg_replace('/[0-]/', '', \substr($string, - 5)); // Replace all 'abc' with 'def'
            // echo $result;

            /*
             * $s = "2-4";
             * $l = strlen($s);
             * $p = strpos($s, "-");
             * echo \substr($s, 0, strpos($s, "-") + 1);
             */
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}