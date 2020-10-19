<?php
namespace Application\Application\Service\Uom;

use Application\Application\Service\Uom\Contracts\UomServiceInterface;
use Application\Application\Service\ValueObject\ValueObjectService;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomSnapshot;
use Application\Infrastructure\Persistence\Doctrine\UomCrudRepositoryImpl;
use Application\Infrastructure\Persistence\Filter\UomSqlFilter;

class UomService extends ValueObjectService implements UomServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\ValueObject\ValueObjectService::setCrudRepository()
     */
    protected function getCrudRepository()
    {
        return new UomCrudRepositoryImpl($this->getDoctrineEM());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\ValueObject\ValueObjectService::setFilter()
     */
    protected function getFilter()
    {
        return new UomSqlFilter();
    }

    protected function createValueObjectFrom($data)
    {
        return Uom::createFromArray($data);
    }
}
