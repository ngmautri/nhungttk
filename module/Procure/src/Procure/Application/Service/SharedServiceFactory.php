<?php
namespace Procure\Application\Service;

use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;

/**
 * AP Service.
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
    static public function create(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        if ($doctrineEM == null) {
            throw new \InvalidArgumentException("EntityManager not found!");
        }

        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
        $postingService = new APPostingService(new APCmdRepositoryImpl($doctrineEM));
        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($$doctrineEM);

        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $domainSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
        $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

        return $sharedService;
    }
}

