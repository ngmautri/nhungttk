<?php
namespace InventoryTest\Item\Rep;

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

    public function testCanGetItem()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new ItemQueryRepositoryImpl($doctrineEM);

            $id = 5763;
            $rootEntity = $rep->getRootEntityById($id);
            \var_dump($rootEntity->getLazyVariantCollection()->count());
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }
}