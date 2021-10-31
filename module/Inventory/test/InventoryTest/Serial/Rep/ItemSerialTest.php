<?php
namespace InventoryTest\Serial\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Domain\Doctrine\ItemSerialQueryRepositoryImpl;
use Inventory\Infrastructure\Persistence\SQL\Filter\ItemSerialSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ItemSerialTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testCanCreateVariant()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new ItemSerialQueryRepositoryImpl($doctrineEM);

            $id = 847;
            $token = "v1sUX9tFOZ_AgNV9pEt6dCj5HmNHCikc";

            $rootEntity = $rep->getByTokenId($id, $token);
            // \var_dump($rootEntity);
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }

    public function testGetListOfItem()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new ItemSerialQueryRepositoryImpl($doctrineEM);

            $filter = new ItemSerialSqlFilter();
            $filter->setItemId(3963);
            $rootEntity = $rep->getlist($filter);

            // \var_dump($rootEntity->getReturn());
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }

    public function testGetListOfInvoice()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new ItemSerialQueryRepositoryImpl($doctrineEM);

            $filter = new ItemSerialSqlFilter();
            $filter->setInvoiceId(1789);
            $rootEntity = $rep->getlistTotal($filter);

            var_dump($rootEntity);
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }
}