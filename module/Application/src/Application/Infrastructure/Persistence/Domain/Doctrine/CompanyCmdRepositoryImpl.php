<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\GenericCompany;
use Application\Domain\Company\AccessControl\BaseRole;
use Application\Domain\Company\AccessControl\Repository\RoleCmdRepositoryInterface;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface;
use Application\Domain\Company\Brand\Repository\BrandCmdRepositoryInterface;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\Department\Repository\DepartmentCmdRepositoryInterface;
use Application\Domain\Company\ItemAssociation\BaseAssociation;
use Application\Domain\Company\ItemAssociation\Repository\ItemAssociationCmdRepositoryInterface;
use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;
use Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface;
use Application\Domain\Company\PostingPeriod\BasePostingPeriod;
use Application\Domain\Company\PostingPeriod\Repository\PostingPeriodCmdRepositoryInterface;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Entity\NmtApplicationCompany;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface;
use User\Domain\User\Repository\UserCmdRepositoryInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyCmdRepositoryImpl extends AbstractDoctrineRepository implements CompanyCmdRepositoryInterface
{

    private $chartCmdRepository;

    private $departmentCmdRepository;

    private $whCmdRepository;

    private $itemAttributeCmdRepository;

    private $itemAssociationCmdRepository;

    private $postingPeriodCmdRepository;

    private $roleCmdRepository;

    private $brandCmdRepository;

    private $userCmdRepository;

    const COMPANY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    public function storeCompany(GenericCompany $company)
    {}

    /*
     * |=================================
     * | Facade Pattern
     * |
     * | delegating to underlying repository.
     * | AccountChart, Warehouse, Department, PostingPeriode, ItemAttribute...;
     * |
     * |==================================
     */

    // +++++++++++++++++++++
    // User Role
    // +++++++++++++++++++++
    public function removeRole(BaseCompany $rootEntity, BaseRole $localEntity, $isPosting = false)
    {
        $this->assertRoleRepository();
        return $this->getRoleCmdRepository()->removeRole($rootEntity, $localEntity);
    }

    public function storeRole(BaseCompany $rootEntity, BaseRole $localEntity, $isPosting = false)
    {
        $this->assertRoleRepository();
        return $this->getRoleCmdRepository()->storeRole($rootEntity, $localEntity);
    }

    // +++++++++++++++++++++
    // Item Assocation
    // +++++++++++++++++++++
    public function storeItemAssociation(BaseCompany $rootEntity, BaseAssociation $localEntity, $isPosting = false)
    {
        $this->assertItemAssociationRepository();
        return $this->getItemAssociationCmdRepository()->storeItemAssociation($rootEntity, $localEntity);
    }

    public function removeItemAssociation(BaseCompany $rootEntity, BaseAssociation $localEntity, $isPosting = false)
    {
        $this->assertItemAssociationRepository();
        return $this->getItemAssociationCmdRepository()->removeItemAssociation($rootEntity, $localEntity);
    }

    // +++++++++++++++++++++
    // Posting Period
    // +++++++++++++++++++++
    public function removePostingPeriod(BaseCompany $rootEntity, BasePostingPeriod $localEntity, $isPosting = false)
    {
        $this->assertPostingPeriodRepository();
        return $this->getPostingPeriodCmdRepository()->storePostingPeriod($rootEntity, $localEntity);
    }

    public function storePostingPeriod(BaseCompany $rootEntity, BasePostingPeriod $localEntity, $isPosting = false)
    {
        $this->assertPostingPeriodRepository();
        return $this->getPostingPeriodCmdRepository()->removePostingPeriod($rootEntity, $localEntity);
    }

    // +++++++++++++++++++++
    // Item Attribute
    // +++++++++++++++++++++

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface::removeAttributeGroup()
     */
    public function removeAttributeGroup(BaseCompany $rootEntity, BaseAttributeGroup $localEntity, $isPosting = false)
    {
        $this->assertItemAttributeRepository();
        return $this->getItemAttributeCmdRepository()->removeAttributeGroup($rootEntity, $localEntity);
    }

    public function storeWholeAttributeGroup(BaseCompany $rootEntity, BaseAttributeGroup $localEntity, $isPosting = false)
    {
        $this->assertItemAttributeRepository();
        return $this->getItemAttributeCmdRepository()->storeWholeAttributeGroup($rootEntity, $localEntity);
    }

    public function storeAttribute(BaseAttributeGroup $rootEntity, BaseAttribute $localEntity, $isPosting = false)
    {
        $this->assertItemAttributeRepository();
        return $this->getItemAttributeCmdRepository()->storeAttribute($rootEntity, $localEntity);
    }

    public function storeAttributeGroup(BaseCompany $rootEntity, BaseAttributeGroup $localEntity, $isPosting = false)
    {
        $this->assertItemAttributeRepository();
        return $this->getItemAttributeCmdRepository()->storeAttributeGroup($rootEntity, $localEntity);
    }

    public function removeAttribute(BaseAttributeGroup $rootEntity, BaseAttribute $localEntity, $isPosting = false)
    {
        $this->assertItemAttributeRepository();
        return $this->getItemAttributeCmdRepository()->removeAttribute($rootEntity, $localEntity);
    }

    // +++++++++++++++++++++
    // Warehouse
    // +++++++++++++++++++++

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface::storeWarehouse()
     */
    public function storeWarehouse(BaseCompany $companyEntity, BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $this->assertWHRepository();
        return $this->getWhCmdRepository()->storeWarehouse($companyEntity, $rootEntity, $generateSysNumber, $isPosting);
    }

    public function RemoveWarehouse(BaseCompany $companyEntity, BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $this->assertWHRepository();
        return $this->getWhCmdRepository()->RemoveWarehouse($companyEntity, $rootEntity, $generateSysNumber, $isPosting);
    }

    public function storeWholeWarehouse(BaseCompany $companyEntity, BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $this->assertWHRepository();
        return $this->getWhCmdRepository()->storeWholeWarehouse($companyEntity, $rootEntity, $generateSysNumber, $isPosting);
    }

    public function storeLocation(BaseWarehouse $rootEntity, BaseLocation $localEntity, $isPosting = false)
    {
        $this->assertWHRepository();
        return $this->getWhCmdRepository()->storeLocation($rootEntity, $localEntity);
    }

    public function removeLocation(BaseWarehouse $rootEntity, BaseLocation $localEntity, $isPosting = false)
    {
        $this->assertWHRepository();
        return $this->getWhCmdRepository()->removeLocation($rootEntity, $localEntity);
    }

    // +++++++++++++++++++++
    // Account Chart
    // +++++++++++++++++++++
    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface::removeChart()
     */
    public function removeChart(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false)
    {
        $this->assertChartRepository();
        return $this->getChartCmdRepository()->removeChart($rootEntity, $localEntity, $isPosting);
    }

    public function storeChart(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false)
    {
        $this->assertChartRepository();
        return $this->getChartCmdRepository()->storeChart($rootEntity, $localEntity, $isPosting);
    }

    public function storeWholeChart(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false)
    {
        $this->assertChartRepository();
        return $this->getChartCmdRepository()->storeWholeChart($rootEntity, $localEntity, $isPosting);
    }

    public function storeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false)
    {
        $this->assertChartRepository();
        return $this->getChartCmdRepository()->storeAccount($rootEntity, $localEntity, $isPosting);
    }

    public function removeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false)
    {
        $this->assertChartRepository();
        return $this->getChartCmdRepository()->removeAccount($rootEntity, $localEntity, $isPosting);
    }

    // +++++++++++++++++++++
    // Department
    // +++++++++++++++++++++
    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyCmdRepositoryInterface::removeDepartment()
     */
    public function removeDepartment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false)
    {
        $this->assertDepartmentRepository();
        return $this->getDepartmentCmdRepository()->removeDepartment($rootEntity, $localSnapshot, $isPosting);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyCmdRepositoryInterface::storeDeparment()
     */
    public function storeDeparment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false)
    {
        $this->assertDepartmentRepository();
        return $this->getDepartmentCmdRepository()->storeDepartment($rootEntity, $localSnapshot, $isPosting);
    }

    /*
     * |=================================
     * | Assert underlying repository
     * |
     * |==================================
     */
    private function assertRoleRepository()
    {
        if ($this->getRoleCmdRepository() == null) {
            throw new InvalidArgumentException("Role repository is not found!");
        }
    }

    private function assertPostingPeriodRepository()
    {
        if ($this->getPostingPeriodCmdRepository() == null) {
            throw new InvalidArgumentException("Posting period repository is not found!");
        }
    }

    private function assertItemAssociationRepository()
    {
        if ($this->getItemAssociationCmdRepository() == null) {
            throw new InvalidArgumentException("Item Association repository is not found!");
        }
    }

    private function assertItemAttributeRepository()
    {
        if ($this->getItemAttributeCmdRepository() == null) {
            throw new InvalidArgumentException("Item Attribute repository is not found!");
        }
    }

    private function assertWHRepository()

    {
        if ($this->getWhCmdRepository() == null) {
            throw new InvalidArgumentException("WH repository is not found!");
        }
    }

    /**
     *
     * @throws InvalidArgumentException
     */
    private function assertDepartmentRepository()
    {
        if ($this->getDepartmentCmdRepository() == null) {
            throw new InvalidArgumentException("Deparment repository is not found!");
        }
    }

    /**
     *
     * @throws InvalidArgumentException
     */
    private function assertChartRepository()
    {
        if ($this->getChartCmdRepository() == null) {
            throw new InvalidArgumentException("Account Chart repository is not found!");
        }
    }

    private function assertAndReturnCompany(BaseCompany $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseCompany not given.");
        }

        /**
         *
         * @var NmtApplicationCompany $rowEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::COMPANNY_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtApplicationCompany) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        return $rootEntityDoctrine;
    }

    /*
     * |=================================
     * | GETTER UNDERLYING REPOSITORY
     * |
     * |==================================
     */
    public function getBrandCmdRepository()
    {
        if (! $this->brandCmdRepository instanceof BrandCmdRepositoryInterface) {
            throw new InvalidArgumentException("Brand repository is not found!");
        }

        return $this->brandCmdRepository;
    }

    /*
     * |=================================
     * | GETTER AND SETTER
     * |
     * |==================================
     */

    /**
     *
     * @return \Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface
     */
    public function getChartCmdRepository()
    {
        return $this->chartCmdRepository;
    }

    /**
     *
     * @param ChartCmdRepositoryInterface $chartCmdRepository
     */
    public function setChartCmdRepository(ChartCmdRepositoryInterface $chartCmdRepository)
    {
        $this->chartCmdRepository = $chartCmdRepository;
    }

    /**
     *
     * @return \Application\Domain\Company\Department\Repository\DepartmentCmdRepositoryInterface
     */
    public function getDepartmentCmdRepository()
    {
        return $this->departmentCmdRepository;
    }

    /**
     * ram DepartmentCmdRepositoryImpl $departmentCmdRepository
     */
    public function setDepartmentCmdRepository(DepartmentCmdRepositoryInterface $departmentCmdRepository)
    {
        $this->departmentCmdRepository = $departmentCmdRepository;
    }

    /**
     *
     * @return \Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface
     */
    public function getWhCmdRepository()
    {
        return $this->whCmdRepository;
    }

    /**
     *
     * @param WhCmdRepositoryInterface $whCmdRepository
     */
    public function setWhCmdRepository(WhCmdRepositoryInterface $whCmdRepository)
    {
        $this->whCmdRepository = $whCmdRepository;
    }

    /**
     *
     * @return \Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface
     */
    public function getItemAttributeCmdRepository()
    {
        return $this->itemAttributeCmdRepository;
    }

    /**
     *
     * @param ItemAttributeCmdRepositoryInterface $itemAttributeCmdRepository
     */
    public function setItemAttributeCmdRepository(ItemAttributeCmdRepositoryInterface $itemAttributeCmdRepository)
    {
        $this->itemAttributeCmdRepository = $itemAttributeCmdRepository;
    }

    /**
     *
     * @return \Application\Domain\Company\ItemAssociation\Repository\ItemAssociationCmdRepositoryInterface
     */
    public function getItemAssociationCmdRepository()
    {
        return $this->itemAssociationCmdRepository;
    }

    /**
     *
     * @param ItemAssociationCmdRepositoryInterface $itemAssociationCmdRepository
     */
    public function setItemAssociationCmdRepository(ItemAssociationCmdRepositoryInterface $itemAssociationCmdRepository)
    {
        $this->itemAssociationCmdRepository = $itemAssociationCmdRepository;
    }

    /**
     *
     * @return \Application\Domain\Company\PostingPeriod\Repository\PostingPeriodCmdRepositoryInterface
     */
    public function getPostingPeriodCmdRepository()
    {
        return $this->postingPeriodCmdRepository;
    }

    /**
     *
     * @param PostingPeriodCmdRepositoryInterface $postingPeriodCmdRepository
     */
    public function setPostingPeriodCmdRepository(PostingPeriodCmdRepositoryInterface $postingPeriodCmdRepository)
    {
        $this->postingPeriodCmdRepository = $postingPeriodCmdRepository;
    }

    /**
     *
     * @return \Application\Domain\Company\AccessControl\Repository\RoleCmdRepositoryInterface
     */
    public function getRoleCmdRepository()
    {
        return $this->roleCmdRepository;
    }

    /**
     *
     * @param RoleCmdRepositoryInterface $roleCmdRepository
     */
    public function setRoleCmdRepository(RoleCmdRepositoryInterface $roleCmdRepository)
    {
        $this->roleCmdRepository = $roleCmdRepository;
    }

    /**
     *
     * @param BrandCmdRepositoryInterface $brandCmdRepository
     */
    public function setBrandCmdRepository(BrandCmdRepositoryInterface $brandCmdRepository)
    {
        $this->brandCmdRepository = $brandCmdRepository;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyCmdRepositoryInterface::getUserCmdRepository()
     */
    public function getUserCmdRepository()
    {
        return $this->userCmdRepository;
    }

    /**
     *
     * @param mixed $userCmdRepository
     */
    public function setUserCmdRepository(UserCmdRepositoryInterface $userCmdRepository)
    {
        $this->userCmdRepository = $userCmdRepository;
    }
}
