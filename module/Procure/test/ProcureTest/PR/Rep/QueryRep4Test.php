<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Infrastructure\Persistence\Domain\Doctrine\PRQueryRepositoryImplV1;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use PHPUnit_Framework_TestCase;

class QueryRep4Test extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            /**
             *
             * @var FilesystemAdapter $cache
             */
            $cache = Bootstrap::getServiceManager()->get('AppCache');

            $id = 1455;
            $token = "d74985c4-3033-46b9-90a1-edbc2b4c0b9c";

            $id = 1459;
            $token = "bea38e13-82a8-405b-90d2-751abaf3093c";

            $id = 1454;
            $token = "4a600b5e-d6bc-43be-86c6-978308aaf746";

            $key = \sprintf("pr_%s_%s", $id, $token);

            $resultCache = $cache->getItem($key);

            if (! $resultCache->isHit()) {

                $cache->deleteItem($key);
                $rep = new PRQueryRepositoryImplV1($doctrineEM);

                $filter = new PrRowReportSqlFilter();
                $result = $rep->getRootEntityByTokenId($id, $token);
                $result->refreshDoc();
                $resultCache->set(serialize($result));
                $resultCache->expiresAfter(100);
                $cache->save($resultCache);
            } else {
                $result = unserialize($cache->getItem($key)->get());
            }

            $p = function ($p) {
                /**
                 *
                 * @var PRRow $p ;
                 */
                return $p->getPostedStandardGrQuantity() > 0;
            };

            $collection = $result->getRowCollection();
            $r = $collection->filter($p);

            var_dump($r->count());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}