<?php
namespace Inventory\Application\Service;

use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use Inventory\Domain\Service\ItemPostingService;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Service\TrxValuationService;
use Inventory\Infrastructure\Doctrine\ItemCmdRepositoryImpl;
use Inventory\Infrastructure\Doctrine\ItemVariantCmdRepositoryImpl;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use Inventory\Infrastructure\Doctrine\WhQueryRepositoryImpl;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

/**
 * Shared Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SharedServiceFactory
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     * @param LoggerInterface $logger
     * @throws \InvalidArgumentException
     * @return \Inventory\Domain\Service\SharedService
     */
    static public function createForTrx(\Doctrine\ORM\EntityManager $doctrineEM, LoggerInterface $logger = null)
    {
        if ($doctrineEM == null) {
            throw new \InvalidArgumentException("EntityManager not found!");
        }

        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);

        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($doctrineEM);

        $cmdRepository = new TrxCmdRepositoryImpl($doctrineEM);
        $postingService = new TrxPostingService($cmdRepository);

        $fifoService = new FIFOServiceImpl();
        $fifoService->setDoctrineEM($doctrineEM);
        $fifoService->setLogger($logger);

        $valuationService = new TrxValuationService($fifoService);

        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $sharedService->setValuationService($valuationService);
        $sharedService->setDomainSpecificationFactory(new InventorySpecificationFactoryImpl($doctrineEM));
        $sharedService->setLogger($logger);
        $sharedService->setWhQueryRepository(new WhQueryRepositoryImpl($doctrineEM));
        return $sharedService;
    }

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     * @param LoggerInterface $logger
     * @return \Inventory\Domain\Service\SharedService
     */
    static public function createForItem(\Doctrine\ORM\EntityManager $doctrineEM, LoggerInterface $logger = null)
    {
        Assert::notNull($doctrineEM, "EntityManager not found!");
        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);

        $companyCmdRepository = new ItemCmdRepositoryImpl($doctrineEM);
        $companyCmdRepository->setItemVariantRepository(new ItemVariantCmdRepositoryImpl($doctrineEM));

        $postingService = new ItemPostingService($companyCmdRepository);
        $sharedService = new SharedService($sharedSpecsFactory, $postingService);

        return $sharedService;
    }
}

