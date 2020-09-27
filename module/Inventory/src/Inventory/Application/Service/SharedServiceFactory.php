<?php
namespace Inventory\Application\Service;

use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Service\TrxValuationService;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use Procure\Domain\Service\SharedService;

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
     */
    static public function createForTrx(\Doctrine\ORM\EntityManager $doctrineEM)
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
        $valuationService = new TrxValuationService($fifoService);

        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $sharedService->setValuationService($valuationService);
        $sharedService->setDomainSpecificationFactory(new InventorySpecificationFactoryImpl($doctrineEM));

        return $sharedService;
    }
}

