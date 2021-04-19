<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\GenericCompany;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\Department\Repository\DepartmentCmdRepositoryInterface;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Entity\NmtApplicationCompany;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
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

    const COMPANY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    public function store(GenericCompany $company)
    {}

    // ================================================================
    // Delegation
    // ================================================================

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyCmdRepositoryInterface::AccountAccountChart()
     */
    public function removeAccountChart(BaseCompany $rootEntity, BaseChart $localEntity)
    {
        $this->assertChartRepository();
        return $this->getChartCmdRepository()->remove($rootEntity, $localEntity);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyCmdRepositoryInterface::storeAccountChart()
     */
    public function storeAccountChart(BaseCompany $rootEntity, BaseChart $localEntity)
    {
        $this->assertChartRepository();
        return $this->getChartCmdRepository()->store($rootEntity, $localEntity);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyCmdRepositoryInterface::storeAccount()
     */
    public function storeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false)
    {
        $this->assertChartRepository();
        return $this->getChartCmdRepository()->storeAccount($rootEntity, $localEntity, $isPosting);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Repository\CompanyCmdRepositoryInterface::removeAccount()
     */
    public function removeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false)
    {
        $this->assertChartRepository();
        return $this->getChartCmdRepository()->removeAccount($rootEntity, $localEntity, $isPosting);
    }

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

    public function storeWarehouse(GenericCompany $company, $warehouse)
    {}

    public function storePostingPeriod(GenericCompany $company)
    {}

    // ==============================================
    // SETTER AND GETTER
    // ==============================================

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
}