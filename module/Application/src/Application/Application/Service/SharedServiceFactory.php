<?php
namespace Application\Application\Service;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Service\CompanyPostingService;
use Application\Domain\Service\SharedService;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyCmdRepositoryImpl;
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
        $postingService = new CompanyPostingService(new CompanyCmdRepositoryImpl($doctrineEM));

        $sharedService = new SharedService($sharedSpecsFactory, $postingService);

        return $sharedService;
    }
}

