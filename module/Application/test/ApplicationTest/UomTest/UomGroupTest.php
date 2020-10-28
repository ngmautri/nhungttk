<?php
namespace ApplicationTest\UomTest;

use ApplicationTest\Bootstrap;
use Application\Domain\Shared\Uom\UomGroup;
use Application\Domain\Shared\Uom\UomGroupSnapshot;
use Application\Domain\Shared\Uom\UomPairSnapshot;
use Application\Infrastructure\Persistence\Doctrine\UomCrudRepositoryImpl;
use PHPUnit_Framework_TestCase;
use Application\Infrastructure\Persistence\Doctrine\UomGroupCrudRepositoryImpl;

class UomGroupTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
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
        $pairSnapshot->counterUom = 'box';
        \var_dump(\strlen($pairSnapshot->counterUom));
        $pairSnapshot->convertFactor = "25";

        $g->createPairFrom($pairSnapshot, $rep);
        /*
         * $baseUom = new Uom('each', 'm');
         * $counterUom = new Uom('meter1', 'm');
         * $convertFactor = 12;
         *
         * $pair = new UomPair($baseUom, $counterUom, $convertFactor);
         * $g->addUomPair($pair);
         */
        // \var_dump($g);
    }
}