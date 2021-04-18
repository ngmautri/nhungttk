<?php
namespace Application\Application\Service;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Service\CompanyPostingService;
use Application\Domain\Service\SharedService;
use Application\Infrastructure\Persistence\Domain\Doctrine\ChartCmdRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyCmdRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\DepartmentCmdRepositoryImpl;
use Webmozart\Assert\Assert;

/**
 * APP Service.
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
    static public function createForCompany(\Doctrine\ORM\EntityManager $doctrineEM)
    {
        Assert::notNull($doctrineEM, "EntityManager not found!");
        $sharedSpecsFactory = new ZendSpecificationFactory($doctrineEM);

        $companyCmdRepository = new CompanyCmdRepositoryImpl($doctrineEM);
        $companyCmdRepository->setChartCmdRepository(new ChartCmdRepositoryImpl($doctrineEM));
        $companyCmdRepository->setDepartmentCmdRepository(new DepartmentCmdRepositoryImpl($doctrineEM));

        $postingService = new CompanyPostingService($companyCmdRepository);
        $sharedService = new SharedService($sharedSpecsFactory, $postingService);

        return $sharedService;
    }
}

