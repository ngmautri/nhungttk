<?php
namespace Application\Domain\Company;

use Application\Domain\Company\AccountChart\ChartSnapshot;
use Application\Domain\Company\AccountChart\Factory\ChartFactory;
use Application\Domain\Company\Collection\AccountChartCollection;
use Application\Domain\Company\Collection\WarehouseCollection;
use Application\Domain\Company\Department\BaseDepartmentSnapshot;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\Department\Factory\DepartmentFactory;
use Application\Domain\Service\SharedService;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Doctrine\Common\Collections\ArrayCollection;
use Closure;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseCompany extends AbstractCompany
{

    protected $warehouseCollection;

    protected $warehouseCollectionRef;

    protected $departmentCollection;

    protected $departmentCollectionRef;

    protected $accountChartCollection;

    protected $accountChartCollectionRef;

    protected $postingPeriodCollection;

    protected $postingPeriodCollectionRef;

    /**
     *
     * @return \Application\Domain\Company\CompanyVO
     */
    public function createValueObject()
    {
        $vo = new CompanyVO();
        GenericObjectAssembler::updateAllFieldsFrom($vo, $this);
        return $vo;
    }

    /**
     *
     * @return \Application\Domain\Company\Collection\WarehouseCollection|mixed
     */
    public function getLazyWarehouseCollection()
    {
        $ref = $this->getWarehouseCollectionRef();
        if (! $ref instanceof Closure) {
            return new WarehouseCollection();
        }

        $this->warehouseCollection = $ref();
        return $this->warehouseCollection;
    }

    public function getLazyDepartmentCollection()
    {
        $ref = $this->getDepartmentCollectionRef();
        if (! $ref instanceof Closure) {
            return new ArrayCollection();
        }

        $this->departmentCollection = $ref();
        return $this->departmentCollection;
    }

    /**
     *
     * @return \Application\Domain\Company\Collection\AccountChartCollection|mixed
     */
    public function getLazyAccountChartCollection()
    {
        $ref = $this->getAccountChartCollectionRef();
        if (! $ref instanceof Closure) {
            return new AccountChartCollection();
        }

        $this->accountChartCollection = $ref();
        return $this->accountChartCollection;
    }

    public function getLazyPostingPeriodCollection()
    {
        $ref = $this->getPostingPeriodCollectionRef();
        if (! $ref instanceof Closure) {
            return new ArrayCollection();
        }

        $this->postingPeriodCollection = $ref();
        return $this->postingPeriodCollection;
    }

    // ==========================================
    // ===========Facade ========================
    // ==========================================
    /**
     *
     * @param ChartSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @param boolean $storeNow
     * @return \Application\Domain\Company\BaseCompany
     */
    public function createAccoutChartFrom(ChartSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        return ChartFactory::createFrom($this, $snapshot, $options, $sharedService);
    }

    /**
     * Implement Facade Pattern
     * delegate to Department Factory
     *
     * @param DepartmentSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Application\Domain\Company\BaseCompany
     */
    public function removeDepartment(DepartmentSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        return DepartmentFactory::remove($this, $snapshot, $options, $sharedService);
    }

    /**
     * Implement Facade Pattern
     * delegate to Department Factory
     *
     * @param BaseDepartmentSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @param boolean $storeNow
     * @throws \RuntimeException
     * @return \Application\Domain\Company\BaseCompany|\Application\Domain\Company\Department\DepartmentSnapshot
     */
    public function createDepartmentFrom(DepartmentSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        return DepartmentFactory::createFrom($this, $snapshot, $options, $sharedService);
    }

    /**
     *
     * @param Closure $warehouseCollectionRef
     */
    public function setWarehouseCollectionRef(Closure $warehouseCollectionRef)
    {
        $this->warehouseCollectionRef = $warehouseCollectionRef;
    }

    // ==========================================
    // ========== Setter and Getter ============
    // ==========================================

    /**
     *
     * @return mixed
     */
    public function getWarehouseCollection()
    {
        return $this->warehouseCollection;
    }

    /**
     *
     * @param mixed $warehouseCollection
     */
    public function setWarehouseCollection($warehouseCollection)
    {
        $this->warehouseCollection = $warehouseCollection;
    }

    /**
     *
     * @return Closure
     */
    public function getWarehouseCollectionRef()
    {
        return $this->warehouseCollectionRef;
    }

    /**
     *
     * @return mixed
     */
    public function getDepartmentCollection()
    {
        return $this->departmentCollection;
    }

    /**
     *
     * @param mixed $departmentCollection
     */
    public function setDepartmentCollection($departmentCollection)
    {
        $this->departmentCollection = $departmentCollection;
    }

    /**
     *
     * @return mixed
     */
    public function getAccountChartCollection()
    {
        return $this->accountChartCollection;
    }

    /**
     *
     * @param mixed $accountChartCollection
     */
    public function setAccountChartCollection($accountChartCollection)
    {
        $this->accountChartCollection = $accountChartCollection;
    }

    /**
     *
     * @return mixed
     */
    public function getPostingPeriods()
    {
        return $this->postingPeriods;
    }

    /**
     *
     * @param mixed $postingPeriods
     */
    public function setPostingPeriods($postingPeriods)
    {
        $this->postingPeriods = $postingPeriods;
    }

    /**
     *
     * @return Closure
     */
    public function getDepartmentCollectionRef()
    {
        return $this->departmentCollectionRef;
    }

    /**
     *
     * @param Closure $departmentCollectionRef
     */
    public function setDepartmentCollectionRef(Closure $departmentCollectionRef)
    {
        $this->departmentCollectionRef = $departmentCollectionRef;
    }

    /**
     *
     * @return Closure
     */
    public function getAccountChartCollectionRef()
    {
        return $this->accountChartCollectionRef;
    }

    /**
     *
     * @param Closure $accountChartCollectionRef
     */
    public function setAccountChartCollectionRef(Closure $accountChartCollectionRef)
    {
        $this->accountChartCollectionRef = $accountChartCollectionRef;
    }

    /**
     *
     * @return mixed
     */
    public function getPostingPeriodCollection()
    {
        return $this->postingPeriodCollection;
    }

    /**
     *
     * @param mixed $postingPeriodCollection
     */
    public function setPostingPeriodCollection($postingPeriodCollection)
    {
        $this->postingPeriodCollection = $postingPeriodCollection;
    }

    /**
     *
     * @return Closure
     */
    public function getPostingPeriodCollectionRef()
    {
        return $this->postingPeriodCollectionRef;
    }

    /**
     *
     * @param Closure $postingPeriodCollectionRef
     */
    public function setPostingPeriodCollectionRef(Closure $postingPeriodCollectionRef)
    {
        $this->postingPeriodCollectionRef = $postingPeriodCollectionRef;
    }
}
