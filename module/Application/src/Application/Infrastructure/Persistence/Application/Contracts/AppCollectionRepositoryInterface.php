<?php
namespace Application\Infrastructure\Persistence\Application\Contracts;

use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AppCollectionRepositoryInterface
{

    public function getCountryCollection(CompanySqlFilterInterface $filter);

    public function getCurrencyCollection(CompanySqlFilterInterface $filter);

    public function getCostCenterCollection(CompanySqlFilterInterface $filter);

    public function getUomCollection(CompanySqlFilterInterface $filter);

    public function getUomGroupCollection(CompanySqlFilterInterface $filter);

    public function getUserCollection(CompanySqlFilterInterface $filter);

    public function getPaymentTermCollection(CompanySqlFilterInterface $filter);

    public function getPaymentMethodCollection(CompanySqlFilterInterface $filter);

    public function getGLAccountCollection(CompanySqlFilterInterface $filter);

    public function getCostCenterAccountCollection(CompanySqlFilterInterface $filter);

    public function getItemAttributeGroupCollection(CompanySqlFilterInterface $filter);

    public function getWHCollection(CompanySqlFilterInterface $filter);
}

