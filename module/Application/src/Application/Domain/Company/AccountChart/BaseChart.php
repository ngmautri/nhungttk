<?php
namespace Application\Domain\Company\AccountChart;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\AccountChart\Tree\AccountChartNode;
use Application\Domain\Company\AccountChart\Tree\DefaultAccountChartTree;
use Application\Domain\Company\AccountChart\Validator\ChartValidatorFactory;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Event\Company\AccountChart\AccountCreated;
use Application\Domain\Service\Contracts\AccountChartValidationServiceInterface;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Doctrine\Common\Collections\ArrayCollection;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Webmozart\Assert\Assert;
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

    public function createAccountFrom(BaseAccountSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        // Assert::notEq($this->getS(), AccountChartStatus::ACTIVE, sprintf("Account chart is not active! %s", $this->getId()));
        Assert::notNull($snapshot, "Account Snapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validationService = ChartValidatorFactory::forCreatingChart($sharedService);
        $snapshot->init($options);
        $snapshot->setCoa($this->getId()); // important;
        $account = GenericAccount::createFromSnapshot($this, $snapshot);

        $this->validateAccount($account, $validationService);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        // Add to tree, in order to check.
        // create node
        $chartTree = $this->createChartTree();
        $root = $chartTree->getRoot();

        if ($account->getParentAccountNumber() == null) {
            $parent = $root;
        } else {
            $parent = $root->getNodeByCode($account->getParentAccountNumber());
        }

        $node = new AccountChartNode();

        $node->setParentCode($snapshot->getParentAccountNumber());
        $node->setParentId($parent->getId());

        $node->setContextObject($snapshot);
        $node->setId($snapshot->getAccountNumber());
        $node->setNodeCode($snapshot->getAccountNumber());
        $node->setNodeName($snapshot->getAccountName());

        // var_dump($root->isNodeDescendant($node));

        $node->setParentId($parent->getId());

        // $parent->add($node); // adding to tree. If ok, go further
        $chartTree->insertAccount($node, $parent, $options);
        $this->clearEvents();
        // $this->addRow($account);

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var APRowSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeAccount($this, $account);

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        // $defaultParams->setTargetDocVersion($this->getDocVersion());
        // $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new AccountCreated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function equals(BaseChart $other)
    {
        if ($other == null) {
            return false;
        }

        return \strtolower(trim($this->getCoaCode())) == \strtolower(trim($other->getCoaCode()));
    }

    public function getLazyAccountCollection()
    {
        $ref = $this->getAccountCollectionRef();
        if (! $ref instanceof Closure) {
            return new ArrayCollection();
        }

        $this->accountCollection = $ref();
        return $this->accountCollection;
    }

    public function getAccountCollection()
    {
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
     * @param BaseAccount $account
     * @param AccountChartValidationServiceInterface $validationService
     * @param boolean $isPosting
     */
    public function validateAccount(BaseAccount $account, AccountChartValidationServiceInterface $validationService, $isPosting = false)
    {
        if ($validationService->getAccountValidators() == null) {
            return;
        }
        $validationService->getAccountValidators()->validate($account);

        if ($account->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    // ==========================================
    // ========== Setter and Getter ============
    // ==========================================

    /**
     *
     * @param ArrayCollection $accountCollection
     */
    public function setAccountCollection(ArrayCollection $accountCollection)
    {
        $this->accountCollection = $accountCollection;
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
