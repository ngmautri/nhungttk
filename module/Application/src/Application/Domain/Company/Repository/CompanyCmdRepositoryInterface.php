<?php
namespace Application\Domain\Company\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\GenericCompany;
use Application\Domain\Company\AccessControl\Repository\RoleCmdRepositoryInterface;
use Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\ItemAssociation\Repository\ItemAssociationCmdRepositoryInterface;
use Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface;
use Application\Domain\Company\PostingPeriod\Repository\PostingPeriodCmdRepositoryInterface;
use Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface;
use User\Domain\User\Repository\UserCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface CompanyCmdRepositoryInterface extends WhCmdRepositoryInterface, ChartCmdRepositoryInterface, ItemAttributeCmdRepositoryInterface, ItemAssociationCmdRepositoryInterface, PostingPeriodCmdRepositoryInterface, RoleCmdRepositoryInterface
{

    /*
     * |=================================
     * | Facade Pattern
     * |
     * | delegating to underlying repository.
     * | AccountChart, Warehouse, Department, PostingPeriode, ItemAttribute...;
     * |
     * |==================================
     */

    /**
     *
     * @return BrandCmdRepositoryInterface;
     */
    public function getBrandCmdRepository();

    /**
     *
     * @return ChartCmdRepositoryInterface;
     */
    public function getChartCmdRepository();

    /**
     *
     * @return DepartmentCmdRepositoryInterface;
     */
    public function getDepartmentCmdRepository();

    /**
     *
     * @return ItemAssociationCmdRepositoryInterface;
     */
    public function getItemAssociationCmdRepository();

    /**
     *
     * @return RoleCmdRepositoryInterface
     */
    public function getRoleCmdRepository();

    /**
     *
     * @return PostingPeriodCmdRepositoryInterface
     */
    public function getPostingPeriodCmdRepository();

    /**
     *
     * @return ItemAttributeCmdRepositoryInterface
     */
    public function getItemAttributeCmdRepository();

    /**
     *
     * @return UserCmdRepositoryInterface
     */
    public function getUserCmdRepository();

    /*
     * |=================================
     * | Facade Pattern
     * |
     * |==================================
     */
    public function storeCompany(GenericCompany $company);

    // ================================================================
    // Delegation
    // ================================================================
    public function storeDeparment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false);

    public function removeDepartment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false);
}
