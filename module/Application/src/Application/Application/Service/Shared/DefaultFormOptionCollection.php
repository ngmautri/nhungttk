<?php
namespace Application\Application\Service\Shared;

use Application\Application\DTO\Common\FormOptionDTO;
use Application\Application\Service\Contracts\AbstractService;
use Application\Application\Service\Contracts\FormOptionCollectionInterface;
use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;
use Application\Entity\MlaUsers;
use Application\Entity\NmtApplicationCountry;
use Application\Entity\NmtInventoryAttributeGroup;
use Application\Entity\NmtInventoryWarehouse;
use Application\Infrastructure\Persistence\Application\Contracts\AppCollectionRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * default collection
 *
 * @author Nguyen Mau Tri
 *        
 */
class DefaultFormOptionCollection extends AbstractService implements FormOptionCollectionInterface
{

    private $appCollectionRepository;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\Application\Contracts\AppCollectionRepositoryInterface::getItemAttributeGroupCollection()
     */
    public function getItemAttributeGroupCollection(CompanySqlFilterInterface $filter)
    {
        $list = $this->getAppCollectionRepository()->getItemAttributeGroupCollection($filter);

        $result = new ArrayCollection();
        if ($list == null) {
            return $result;
        }

        /**
         *
         * @var NmtInventoryAttributeGroup $l ;
         */
        foreach ($list as $l) {
            $result->add($this->createElement($l->getId(), \sprintf('%s %s', $l->getGroupName(), '')));
        }
        return $result;
    }

    public function getPaymentTermCollection(CompanySqlFilterInterface $filter)
    {}

    public function getCostCenterCollection(CompanySqlFilterInterface $filter)
    {}

    public function getCurrencyCollection(CompanySqlFilterInterface $filter)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Contracts\FormOptionCollectionInterface::getUserCollection()
     */
    public function getUserCollection(CompanySqlFilterInterface $filter)
    {
        $list = $this->getAppCollectionRepository()->getUserCollection($filter);

        $result = new ArrayCollection();
        if ($list == null) {
            return $result;
        }

        /**
         *
         * @var MlaUsers $l ;
         */
        foreach ($list as $l) {
            $result->add($this->createElement($l->getId(), \sprintf('%s %s', $l->getFirstname(), $l->getLastname())));
        }
        return $result;
    }

    public function getGLAccountCollection(CompanySqlFilterInterface $filter)
    {}

    public function getCostCenterAccountCollection(CompanySqlFilterInterface $filter)
    {}

    public function getUomCollection(CompanySqlFilterInterface $filter)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Contracts\FormOptionCollectionInterface::getCountryCollection()
     */
    public function getCountryCollection(CompanySqlFilterInterface $filter)
    {
        $list = $this->getAppCollectionRepository()->getCountryCollection($filter);

        $result = new ArrayCollection();
        if ($list == null) {
            return $result;
        }

        /**
         *
         * @var NmtApplicationCountry $l ;
         */
        foreach ($list as $l) {
            $result->add($this->createElement($l->getId(), \sprintf('%s %s', $l->getCountryName(), $l->getCountryCode2())));
        }
        return $result;
    }

    public function getPaymentMethodCollection(CompanySqlFilterInterface $filter)
    {}

    public function getUomGroupCollection(CompanySqlFilterInterface $filter)
    {}

    /**
     *
     * @return \Application\Infrastructure\Persistence\Application\Contracts\AppCollectionRepositoryInterface
     */
    public function getAppCollectionRepository()
    {
        return $this->appCollectionRepository;
    }

    public function getWHCollection(CompanySqlFilterInterface $filter)
    {
        $list = $this->getAppCollectionRepository()->getWHCollection($filter);

        $result = new ArrayCollection();
        if ($list == null) {
            return $result;
        }

        /**
         *
         * @var NmtInventoryWarehouse $l ;
         */
        foreach ($list as $l) {
            $result->add($this->createElement($l->getId(), \sprintf('%s %s', $l->getWhName(), $l->getWhCode())));
        }
        return $result;
    }

    /**
     *
     * @param AppCollectionRepositoryInterface $appCollectionRepository
     */
    public function setAppCollectionRepository(AppCollectionRepositoryInterface $appCollectionRepository)
    {
        $this->appCollectionRepository = $appCollectionRepository;
    }

    /*
     * |=============================
     * |Private
     * |
     * |=============================
     */

    /**
     *
     * @param string $v
     * @param string $n
     * @return \Application\Application\DTO\Common\FormOptionDTO
     */
    private function createElement($v, $n)
    {
        $e = new FormOptionDTO();
        $e->setValue($v);
        $e->setName($n);
        return $e;
    }
}