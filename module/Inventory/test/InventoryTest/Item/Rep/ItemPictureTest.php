<?php
namespace InventoryTest\Item\Rep;

use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ItemVariantsTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testCanCreateVariant()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new CompanyQueryRepositoryImpl($doctrineEM);
            // $filter = new CompanyQuerySqlFilter();
            $company = $rep->getById(1);
            $companyVO = $company->createValueObject();

            $rep = new ItemQueryRepositoryImpl($doctrineEM);

            $id = 5763;
            $token = "fbf18488-7fb1-403a-927a-6589b0a038c1";

            $rootEntity = $rep->getRootEntityById($id);
            \var_dump($rootEntity->makeSnapshot()->getStatistics());
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }
}