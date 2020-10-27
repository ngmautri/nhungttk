<?php
namespace ApplicationTest\UomTest;

use ApplicationTest\Bootstrap;
use Application\Domain\Shared\Uom\UomGroup;
use Application\Domain\Shared\Uom\UomGroupSnapshot;
use Application\Domain\Shared\Uom\UomPairSnapshot;
use Application\Infrastructure\Persistence\Doctrine\UomCrudRepositoryImpl;
use PHPUnit_Framework_TestCase;
use Application\Infrastructure\Persistence\Doctrine\UomGroupCrudRepositoryImpl;
use Application\Domain\Shared\Uom\UomPair;

class UomGroupTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $pairSnapshot = new UomPairSnapshot();
        $pairSnapshot->baseUom = 'each';
        $pairSnapshot->counterUom = 'box';
        $pairSnapshot->convertFactor = 12;
        $pair1 = UomPair::createFrom($pairSnapshot);
        $pair2 = UomPair::createFrom($pairSnapshot);

        \var_dump($pair1->equals($pair2));
    }
}