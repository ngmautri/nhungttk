<?php
namespace Application\Infrastructure\Persistence\Application\Doctrine;

use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Application\Contracts\AppCollectionRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AppCollectionRepositoryImpl extends AbstractDoctrineRepository implements AppCollectionRepositoryInterface
{

    public function getPaymentTermCollection(CompanySqlFilterInterface $filter)
    {}

    public function getCostCenterCollection(CompanySqlFilterInterface $filter)
    {}

    public function getCurrencyCollection(CompanySqlFilterInterface $filter)
    {}

    /**
     *
     * @param CompanySqlFilterInterface $filter
     * @return array
     */
    public function getUserCollection(CompanySqlFilterInterface $filter)
    {
        $criteria = [
            'block' => 0
        ];

        if ($filter->getCompanyId() > 0) {
            $criteria = [
                'block' => 0,
                'company' => $filter->getCompanyId()
            ];
        }

        $sort_criteria = [];

        $list = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findBy($criteria, $sort_criteria);
        return $list;
    }

    public function getGLAccountCollection(CompanySqlFilterInterface $filter)
    {}

    public function getCostCenterAccountCollection(CompanySqlFilterInterface $filter)
    {}

    public function getUomCollection(CompanySqlFilterInterface $filter)
    {}

    public function getCountryCollection(CompanySqlFilterInterface $filter)
    {
        $criteria = [
            'isActive' => 1
        ];

        $sort_criteria = [
            'countryName' => 'ASC'
        ];

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findBy($criteria, $sort_criteria);
        return $list;
    }

    public function getPaymentMethodCollection(CompanySqlFilterInterface $filter)
    {}

    public function getUomGroupCollection(CompanySqlFilterInterface $filter)
    {}
}