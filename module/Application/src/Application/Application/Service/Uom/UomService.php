<?php
namespace Application\Application\Service\Uom;

use Application\Application\Service\Uom\Contracts\UomServiceInterface;
use Application\Application\Service\ValueObject\ValueObjectService;
use Application\Infrastructure\Persistence\Doctrine\UomCrudRepositoryImpl;

class UomService extends ValueObjectService implements UomServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\ValueObject\ValueObjectService::setCrudRepository()
     */
    protected function setCrudRepository()
    {
        $this->crudRepository = new UomCrudRepositoryImpl($this->getDoctrineEM());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\ValueObject\ValueObjectService::setFilter()
     */
    protected function setFilter()
    {}
}
