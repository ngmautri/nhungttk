
<?php
namespace Procure\Application\Service;

use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\ClearingDocPostingService;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\PRPostingService;
use Procure\Domain\Service\QrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\GRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\POCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\QRCmdRepositoryImpl;
use Procure\Infrastructure\Persistence\Domain\Doctrine\ClearingCmdRepositoryImpl;
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

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     * @return \Procure\Domain\Service\SharedService
     */
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

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     * @return \Procure\Domain\Service\SharedService
     */
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

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     * @return \Procure\Domain\Service\SharedService
     */
    static public function createForPR(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        Assert::notNull($doctrineEM, "EntityManager not found!");

        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
        $postingService = new PRPostingService(new PRCmdRepositoryImpl($doctrineEM));
        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($doctrineEM);
        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $domainSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
        $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

        return $sharedService;
    }

    static public function createForQR(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        Assert::notNull($doctrineEM, "EntityManager not found!");

        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
        $postingService = new QrPostingService(new QRCmdRepositoryImpl($doctrineEM));
        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($doctrineEM);
        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $domainSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
        $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

        return $sharedService;
    }

    static public function createForClearingDoc(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        Assert::notNull($doctrineEM, "EntityManager not found!");

        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);
        $postingService = new ClearingDocPostingService(new ClearingCmdRepositoryImpl($doctrineEM));
        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($doctrineEM);
        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $domainSpecsFactory = new ProcureSpecificationFactory($doctrineEM);
        $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

        return $sharedService;
    }
}

