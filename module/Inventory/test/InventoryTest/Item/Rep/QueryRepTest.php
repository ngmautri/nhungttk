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
            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
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
            var_dump($e->getMessage());
        }
    }
}