<?php
namespace Procure\Application\Service;

use Application\Service\AbstractService;
use Procure\Domain\Service\QueryRepositoryFactoryInteface;
use Procure\Infrastructure\Doctrine\DoctrineAPDocQueryRepository;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QueryRepositoryFactory extends AbstractService implements QueryRepositoryFactoryInteface
{

    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Domain\Service\QueryRepositoryFactoryInteface::createAPQueryRepository()
     */
    public function createAPQueryRepository()
    {
        return new DoctrineAPDocQueryRepository($this->getDoctrineEM());
    }

    public function createPRQueryRepository()
    {
         
    }

    public function createQRQueryRepository()
    {}

    public function createPOQueryRepository()
    {
        return new DoctrinePOQueryRepository($this->getDoctrineEM());
        
    }
}
