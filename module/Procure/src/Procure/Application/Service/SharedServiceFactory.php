<?php
namespace Procure\Application\Service;

use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\GRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\POCmdRepositoryImpl;
use Webmozart\Assert\Assert;

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
    static public function createForAP(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        Assert::notNull($doctrineEM, "EntityManager not found!");
        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
        $postingService = new APPostingService(new APCmdRepositoryImpl($doctrineEM));
        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($doctrineEM);

        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $domainSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
        $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

        return $sharedService;
    }

    static public function createForGR(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        Assert::notNull($doctrineEM, "EntityManager not found!");
        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
        $postingService = new GrPostingService(new GRCmdRepositoryImpl($doctrineEM));
        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($doctrineEM);

        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $domainSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
        $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

        return $sharedService;
    }

    static public function createForPO(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        Assert::notNull($doctrineEM, "EntityManager not found!");

        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
        $postingService = new POPostingService(new POCmdRepositoryImpl($doctrineEM));
        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($doctrineEM);
        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $domainSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
        $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

        return $sharedService;
    }
}

