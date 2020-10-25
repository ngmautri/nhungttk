<?php
namespace InventoryTest\Item\Rep;

use Application\Infrastructure\Persistence\Doctrine\UomCrudRepositoryImpl;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Application\Application\Contracts\GenericSnapshotAssembler;

class UomRep1Test extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new UomCrudRepositoryImpl($doctrineEM);
            $result = $rep->getByKey("ton");
            $snapShot = $result->makeSnapshot();

            $snapShot1 = clone $snapShot;

            $data = [
                "uomCode" => "ton",
                "uomName" => "ton"
            ];
            $excludedProperties = [];
            $snapShot1 = GenericSnapshotAssembler::updateSnapshotFromArrayExcludeFields($snapShot1, $data, $excludedProperties);

            \var_dump($snapShot->compare($snapShot1));

            // \var_dump($snapShot1);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}