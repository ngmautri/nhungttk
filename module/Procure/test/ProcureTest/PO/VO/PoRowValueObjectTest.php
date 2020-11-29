<?php
namespace ProcureTest\PO\VO;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\PO\RowValueObjectFactory;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use PHPUnit_Framework_TestCase;
use Procure\Application\Service\PO\RowSnapshotModifier;

class PoRowValueObjectTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $data["item"] = 4431;
            $data["isActive"] = 1;
            $data["docCurrencyISO"] = 'USD';
            $data["docUnit"] = 'each';
            $data["docQuantity"] = '10';
            $data["docUnitPrice"] = '25.4898';
            $data["conversionFactor"] = '1.5';

            $snapshot = new PORowSnapshot();
            $snapshot = GenericSnapshotAssembler::createSnapshotFromArray($data, $snapshot);
            RowSnapshotModifier::updateFrom($snapshot, $doctrineEM, 'en_EN');
            // var_dump($snapshot);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}

