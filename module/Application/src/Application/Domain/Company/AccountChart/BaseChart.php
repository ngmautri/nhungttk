<?php
namespace Application\Domain\Company\AccountChart;

use Application\Domain\Company\AccountChart\Tree\DefaultAccountChartTree;
use Application\Domain\Service\Contracts\AccountChartValidationServiceInterface;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Doctrine\Common\Collections\ArrayCollection;
use Closure;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseChart extends AbstractChart
{

    private $accountCollection;

    private $accountCollectionRef;

    public function equals(BaseChart $other)
    {
        if ($other == null) {
            return false;
        }

        \var_dump($this->getCoaCode() . '===' . $other->getCoaCode());
        return \strtolower(trim($this->getCoaCode())) == \strtolower(trim($other->getCoaCode()));
    }

    public function getAccountCollection()
    {
        $ref = $this->getAccountCollectionRef();
        if (! $ref instanceof Closure) {
            return new ArrayCollection();
        }

        $this->accountCollection = $ref();
        return $this->accountCollection;
    }

    public function createChartTree()
    {
        $tree = new DefaultAccountChartTree($this);
        $tree->initTree();
        $tree->createTree($this->getCoaCode(), 0);
        return $tree;
    }

    /**
     *
     * @return \Application\Domain\Company\AccountChart\BaseChartSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new BaseChartSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }

    /**
     *
     * @param AccountChartValidationServiceInterface $validatorService
     * @param boolean $isPosting
     */
    public function validate(AccountChartValidationServiceInterface $validatorService, $isPosting = false)
    {
        $this->validateChart($validatorService->getChartValidators());
    /**
     *
     * @todo: validate account.
     */
    }

    /**
     *
     * @param AccountChartValidationServiceInterface $validatorService
     * @param boolean $isPosting
     */
    public function validateChart(AccountChartValidationServiceInterface $validatorService, $isPosting = false)
    {
        $validatorService->getChartValidators()->validate($this);

        if ($this->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    /**
     *
     * @param AccountChartValidationServiceInterface $validatorService
     * @param boolean $isPosting
     */
    public function validateAccount(AccountChartValidationServiceInterface $validatorService, $isPosting = false)
    {
        $validatorService->getChartValidators()->validate($this);

        if ($this->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    // ==========================================
    // ========== Setter and Getter ============
    // ==========================================

    /**
     *
     * @param ArrayCollection $accountList
     */
    public function setAccountCollection(ArrayCollection $accountList)
    {
        $this->accountCollection = $accountList;
    }

    /**
     *
     * @return Closure
     */
    public function getAccountCollectionRef()
    {
        return $this->accountCollectionRef;
    }

    /**
     *
     * @param Closure $accountCollectionRef
     */
    public function setAccountCollectionRef(Closure $accountCollectionRef)
    {
        $this->accountCollectionRef = $accountCollectionRef;
    }
}
