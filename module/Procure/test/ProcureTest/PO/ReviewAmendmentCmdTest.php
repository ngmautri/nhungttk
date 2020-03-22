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
use Procure\Application\Command\PO\CreateHeaderCmd;
use Procure\Application\Command\PO\CreateHeaderCmdHandler;
use Application\Domain\Shared\DTOFactory;
use Procure\Application\DTO\Po\PoDTO;
use Application\Notification;
use Procure\Application\Command\PO\AddRowCmd;
use Procure\Application\Command\PO\AddRowCmdHandler;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Application\Command\PO\AcceptAmendmentCmd;
use Procure\Application\Command\PO\AcceptAmendmentCmdHandler;
use Procure\Application\Command\PO\Options\PoAmendmentAcceptOptions;

class ReviewAmendmentTest extends PHPUnit_Framework_TestCase
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
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
             
            
          
            $entity_id = 331;
            $entity_token = "12c6efa7-35fe-45d4-abe8-6b218d864f76";
            $version = 23;
            $userId = 39;
            $rootEntity= $sv->getPODetailsById($entity_id,$entity_token);
            
            $options = new PoAmendmentAcceptOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);
            
            
            $dto = new PoDTO();
            
            $cmd = new AcceptAmendmentCmd($doctrineEM, $dto, $options, new AcceptAmendmentCmdHandler());
            $cmd->execute();
               
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}