<?php
namespace ProcureTest\PO\Search;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\Search\ZendSearch\PR\PrSearchQueryImpl;
use Procure\Application\Service\Search\ZendSearch\PR\Filter\PrQueryFilter;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\PRRowSnapshotAssembler;
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

            $searcher = new PrSearchQueryImpl();
            $queryFilter = new PrQueryFilter();
            // $queryFilter->setDocStatus("posted");
            $results = $searcher->search("paint", $queryFilter);

            $results_array = [];

            $results_array['message'] = $results->getMessage();
            $results_array['total_hits'] = $results->getTotalHits();

            $hits_array = [];
            $n = 0;
            foreach ($results->getHits() as $hit) {
                $n ++;
                $item_thumbnail = '/images/no-pic1.jpg';
                if ($hit->itemId != null) {
                    $item_thumbnail = "test";
                }
                $hits_array["item_thumbnail"] = $item_thumbnail;

                $hits_array["n"] = $n;
                $hits_array["value"] = \sprintf('%s | %s', $hit->docNumber, $hit->itemName);
                $hits_array["hit"] = PRRowSnapshotAssembler::createFromQueryHit($hit);
            }

            $results_array['hits'] = $hits_array;

            var_dump(\json_encode($results_array));
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}