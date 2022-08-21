<?php
namespace HR\Application\Service;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use HR\Application\Specification\Zend\IndividualSpecificationFactoryImpl;
use HR\Domain\Service\HRDomainSpecificationFactory;
use HR\Domain\Service\IndividualPostingService;
use HR\Domain\Service\SharedService;
use HR\Infrastructure\Persistence\Domain\Doctrine\IndividualCmdRepositoryImpl;
use Psr\Log\LoggerInterface;

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
    static public function createForIndividual(\Doctrine\ORM\EntityManager $doctrineEM, LoggerInterface $logger = null)
    {
        if ($doctrineEM == null) {
            throw new \InvalidArgumentException("EntityManager not found!");
        }

        $sharedSpecificationFactory = new ZendSpecificationFactory($doctrineEM);

        $cmdRepository = new IndividualCmdRepositoryImpl($doctrineEM);
        $postingService = new IndividualPostingService($cmdRepository);

        $sharedService = new SharedService($sharedSpecificationFactory, $postingService);

        $domainSpecificationFactory = new HRDomainSpecificationFactory();
        $domainSpecificationFactory->setIndividualSpecificationFactory(new IndividualSpecificationFactoryImpl($doctrineEM));

        $sharedService->setDomainSpecificationFactory($domainSpecificationFactory);
        $sharedService->setLogger($logger);
        return $sharedService;
    }
}

