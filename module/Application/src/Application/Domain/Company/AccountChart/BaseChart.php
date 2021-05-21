<?php
namespace Application\Domain\Company\AccountChart;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface;
use Application\Domain\Company\AccountChart\Tree\AccountChartNode;
use Application\Domain\Company\AccountChart\Tree\DefaultAccountChartTree;
use Application\Domain\Company\AccountChart\Validator\ChartValidatorFactory;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Event\Company\AccountChart\AccountCreated;
use Application\Domain\Event\Company\AccountChart\AccountRemoved;
use Application\Domain\Event\Company\AccountChart\AccountUpdated;
use Application\Domain\Service\SharedService;
use Application\Domain\Service\Contracts\AccountChartValidationServiceInterface;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
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

    public function store(SharedService $sharedService)
    {
        Assert::notNull($sharedService, Translator::translate(sprintf("Shared Service not set! %s", __FUNCTION__)));

        /**
         *
         * @var CompanyCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();

        $this->setLogger($sharedService->getLogger());
        $validationService = ChartValidatorFactory::forCreatingChart($sharedService);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $rep->storeWholeAccountChart($this);

        $this->logInfo(\sprintf("Account chart saved %s", __METHOD__));
        return $this;
    }

    /**
     *
     * @param BaseAccount $localEntity
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @return \Application\Domain\Company\AccountChart\BaseChart
     */
    public function removeAccount(BaseAccount $localEntity, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::notNull($localEntity, "BaseAccount not found");
        Assert::notNull($options, "Options not founds");
        Assert::notNull($sharedService, "SharedService not found");

        /**
         *
         * @var ChartCmdRepositoryInterface $rep ;
         *
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->removeAccount($this, $localEntity);

        $params = null;

        $target = $localEntity->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($localEntity->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new AccountRemoved($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    public function getAccountById($id)
    {
        $collection = $this->getLazyAccountCollection();
        if ($collection->isEmpty()) {
            throw new \InvalidArgumentException("Account id [$id] not found!");
        }

        foreach ($collection as $e) {
            /**
             *
             * @var BaseAccount $e
             */
            if ($e->getId() == $id) {
                return $e;
            }
        }

        throw new \InvalidArgumentException("Account id [$id] not found!");
    }

    public function getAccountByNumber($number)
    {
        $collection = $this->getLazyAccountCollection();
        if ($collection->isEmpty()) {
            throw new \InvalidArgumentException("Account number [$number] not found!");
        }

        foreach ($collection as $e) {
            /**
             *
             * @var BaseAccount $e
             */
            if (\strtolower(trim($e->getAccountNumber())) == \strtolower(trim($number))) {
                return $e;
            }
        }

        throw new \InvalidArgumentException("Account number [$number] not found!");
    }

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

        $parent = $root;
        if ($root->getNodeByCode($account->getParentAccountNumber()) != null) {
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
        $this->getAccountCollection()->add($account);

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

    /**
     *
     * @param BaseAccount $account
     * @param BaseAccountSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @param boolean $storeNow
     * @throws \RuntimeException
     * @return \Application\Domain\Company\AccountChart\BaseChart|\Application\Domain\Company\AccountChart\BaseAccount
     */
    public function updateAccountFrom(BaseAccount $account, BaseAccountSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        Assert::notNull($account, Translator::translate("Account entity not found!"));
        Assert::notNull($snapshot, Translator::translate("Account snapshot not found!"));

        // Add to tree, in order to check.
        // create node
        $chartTree = $this->createChartTree();
        $root = $chartTree->getRoot();

        $node = $root->getNodeByCode($account->getAccountNumber());

        // try to change code. If ok, go further
        $chartTree->changeAccountNumber($node, $snapshot->getAccountNumber(), $options);

        $validationService = ChartValidatorFactory::forCreatingAccount($sharedService);

        AccountSnapshotAssembler::updateDefaultExcludedFieldsFrom($snapshot, $account);

        $this->validateAccount($account, $validationService);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        // $this->addRow($account);

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var BaseAccount $localSnapshot ;
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

        $event = new AccountUpdated($target, $defaultParams, $params);
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

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
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
        if ($this->accountCollection == null) {
            return new ArrayCollection();
        }
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
        $this->validateChart($validatorService);
        foreach ($this->getAccountCollection() as $account) {
            $this->validateAccount($account, $validatorService);
        }
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
        $validationService->getAccountValidators()->validate($this, $account);

        if ($account->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    /*
     * |=================================
     * | GETTER AND SETTER
     * |
     * |==================================
     */

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
