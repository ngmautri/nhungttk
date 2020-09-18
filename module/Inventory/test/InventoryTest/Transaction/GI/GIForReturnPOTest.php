<?php
namespace InventoryTest\Transatcion\GI;

use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Doctrine\ORM\EntityManager;
use Inventory\Application\Command\Transaction\Options\CopyFromGROptions;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Service\TrxValuationService;
use Inventory\Domain\Transaction\GI\GIforReturnPO;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class GIForReturnPOTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new TrxQueryRepositoryImpl($doctrineEM);

            $id = 1527;
            $token = "07f44075-a3c2-44fa-86b4-867a36b63442";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            $companyId = 1;
            $userId = 39;

            $options = new CopyFromGROptions($companyId, $userId, __METHOD__);

            $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);

            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($doctrineEM);

            $cmdRepository = new TrxCmdRepositoryImpl($doctrineEM);
            $postingService = new TrxPostingService($cmdRepository);

            $fifoService = new FIFOServiceImpl();
            $fifoService->setDoctrineEM($doctrineEM);
            $valuationService = new TrxValuationService($fifoService);

            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
            $sharedService->setValuationService($valuationService);
            $sharedService->setDomainSpecificationFactory(new InventorySpecificationFactoryImpl($doctrineEM));

            $gm = GIforReturnPO::createFromGRFromPurchasing($rootEntity, $options, $sharedService);
            var_dump($gm->getRelevantMovementId());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}