<?php
namespace ApplicationTest\UomTest;

use ApplicationTest\Bootstrap;
use Application\Domain\Shared\Uom\UomPairSnapshot;
use Application\Infrastructure\Persistence\Doctrine\UomGroupCrudRepositoryImpl;
use Application\Infrastructure\Persistence\Filter\DefaultListSqlFilter;
use PHPUnit_Framework_TestCase;

class UomGroupTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testGetMember()
    {
        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $rep = new UomGroupCrudRepositoryImpl($doctrineEM);
        $g = $rep->getByKey('QUANTITY UOM');
        $this->assertNotNull($g);
    }

    public function testCreateFailed()
    {
        /*
         * $snapshot = new UomGroupSnapshot();
         * $snapshot->setGroupName("Volumn Group");
         * $snapshot->setBaseUom("each");
         * $snapshot->setCompany(1);
         * $g = UomGroup::createFrom($snapshot);
         */
        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $rep = new UomGroupCrudRepositoryImpl($doctrineEM);
        $g = $rep->getByKey('QUANTITY UOM');
        // \var_dump($g);

        $pairSnapshot = new UomPairSnapshot();
        $pairSnapshot->counterUom = 'pack';
        // \var_dump(\strlen($pairSnapshot->counterUom));
        $pairSnapshot->convertFactor = "200";

        $this->expectException("InvalidArgumentException");
        $g->createPairFrom($pairSnapshot, $rep);
    }

    public function testGetList()
    {
        /*
         * $snapshot = new UomGroupSnapshot();
         * $snapshot->setGroupName("Volumn Group");
         * $snapshot->setBaseUom("each");
         * $snapshot->setCompany(1);
         * $g = UomGroup::createFrom($snapshot);
         */
        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $rep = new UomGroupCrudRepositoryImpl($doctrineEM);

        $filter = new DefaultListSqlFilter();
        $filter->setCompanyId(2);
        $g = $rep->getList($filter);
        echo $g->count();
    }
}