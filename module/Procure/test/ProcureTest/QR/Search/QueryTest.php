<?php
namespace ProcureTest\QR\Search;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\Search\ZendSearch\QR\QrSearchQueryImpl;
use Procure\Application\Service\Search\ZendSearch\QR\Filter\QrQueryFilter;
use Procure\Domain\QuotationRequest\QRRowSnapshotAssembler;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class QueryTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $searcher = new QrSearchQueryImpl();
            $queryFilter = new QrQueryFilter();
            $queryFilter->setDocStatus("posted");
            $hits = $searcher->search("077*", $queryFilter);
            echo ($hits->getMessage());
            echo ($hits->getQueryString());
            $results = [];
            foreach ($hits->getHits() as $hit) {
                $results[] = QRRowSnapshotAssembler::createFromQueryHit($hit);
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