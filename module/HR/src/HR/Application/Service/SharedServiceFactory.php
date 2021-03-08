<?php
namespace HR\Application\Service;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use HR\Application\Specification\Zend\HRSpecificationFactoryImpl;
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
        $sharedService->setDomainSpecificationFactory(new HRSpecificationFactoryImpl($doctrineEM));
        $sharedService->setLogger($logger);
        return $sharedService;
    }
}

