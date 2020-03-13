<?php
namespace ProcureTest\PO;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\FXService;
use Procure\Application\Service\PO\POService;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\Service\POSpecService;
use PHPUnit_Framework_TestCase;

class POServiceTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {
           
            /** @var POService $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
            $po= $sv->getPODetailsById(302,"b69a9fbe-e7e5-48da-a7a7-cf7e27040d1b");
            
            $id = "2484";
            $token="effecea7-b949-4be2-9e1c-aba40ea844e0";
            var_dump($po->getRowbyTokenId($id, $token));
           
            
            
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}