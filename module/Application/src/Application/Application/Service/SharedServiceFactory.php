<?php
namespace Application\Application\Service;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Service\CompanyPostingService;
use Application\Domain\Service\SharedService;
use Application\Infrastructure\Persistence\Domain\Doctrine\BrandCmdRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\ChartCmdRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyCmdRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\DepartmentCmdRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\ItemAssociationCmdRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\ItemAttributeCmdRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\PostingPeriodCmdRepositoryImpl;
use Inventory\Infrastructure\Doctrine\WhCmdRepositoryImpl;
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

        // Adding underling repository
        $companyCmdRepository->setChartCmdRepository(new ChartCmdRepositoryImpl($doctrineEM));
        $companyCmdRepository->setDepartmentCmdRepository(new DepartmentCmdRepositoryImpl($doctrineEM));
        $companyCmdRepository->setWhCmdRepository(new WhCmdRepositoryImpl($doctrineEM));
        $companyCmdRepository->setItemAttributeCmdRepository(new ItemAttributeCmdRepositoryImpl($doctrineEM));
        $companyCmdRepository->setItemAssociationCmdRepository(new ItemAssociationCmdRepositoryImpl($doctrineEM));
        $companyCmdRepository->setPostingPeriodCmdRepository(new PostingPeriodCmdRepositoryImpl($doctrineEM));
        $companyCmdRepository->setBrandCmdRepository(new BrandCmdRepositoryImpl($doctrineEM));

        $postingService = new CompanyPostingService($companyCmdRepository);
        $sharedService = new SharedService($sharedSpecsFactory, $postingService);

        return $sharedService;
    }
}

