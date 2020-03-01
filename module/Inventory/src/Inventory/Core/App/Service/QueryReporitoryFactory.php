<?php
namespace Inventory\Application\Service;

use Application\Service\AbstractService;
use Inventory\Domain\Service\QueryRepositoryFactoryInteface;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseQueryRepository;
use Inventory\Infrastructure\Doctrine\DoctrineTransactionQueryRepository;
use Inventory\Infrastructure\Doctrine\DoctrineItemRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QueryRepositoryFactory extends AbstractService implements QueryRepositoryFactoryInteface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\QueryRepositoryFactoryInteface::createWarehouseQueryRepository()
     */
    public function createWarehouseQueryRepository()
    {
        return new DoctrineWarehouseQueryRepository($this->getDoctrineEM());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\QueryRepositoryFactoryInteface::createTransactionQueryRepository()
     */
    public function createTransactionQueryRepository()
    {
        return new DoctrineTransactionQueryRepository($this->getDoctrineEM());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\QueryRepositoryFactoryInteface::createItemQueryRepository()
     */
    public function createItemQueryRepository()
    {
        return new DoctrineItemRepository($this->getDoctrineEM());
    }
}
