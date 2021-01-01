<?php
namespace InventoryTest\Item\Rep;

use Application\Entity\NmtInventoryFifoLayer;
use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new ItemQueryRepositoryImpl($doctrineEM);

            $id = 1010;
            $token = "gFPYQewcor_DUbWWl8FUFouBwdGV4JQN";

            // $id = 5080;
            // $token = "039712b8-753e-4924-8025-94e9e8432fe5";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            // var_dump($rootEntity->getBaseUom());
            $fifo = $rootEntity->getFifoLayerList();

            foreach ($fifo as $f) {
                /**
                 *
                 * @var NmtInventoryFifoLayer $f ;
                 */
                echo $f->getPostingDate()->format("d-M-Y") . "\n";
                echo $f->getOnhandQuantity() . "\n";

                if ($f->getWarehouse()) {
                    echo $f->getWarehouse()->getWhName() . "\n";
                }
            }
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }

    public function testCanGetItem()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new ItemQueryRepositoryImpl($doctrineEM);

            $id = 5455;
            $token = "74058b2a-1142-4259-8910-3178e4e998a5";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            \var_dump($rootEntity->getStandardUnitName());
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }
}