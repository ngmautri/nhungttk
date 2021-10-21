<?php
namespace Procure\Domain\Clearing;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Doctrine\Common\Collections\ArrayCollection;
use Procure\Domain\Clearing\Repository\ClearingCmdRepositoryInterface;
use Procure\Domain\Clearing\Validator\ValidatorFactory;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Event\Clearing\ClearingDocPosted;
use Procure\Domain\Event\Clearing\ClearingRowAdded;
use Procure\Domain\Event\Clearing\ClearingRowUpdated;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Procure\Domain\PurchaseRequest\Repository\PrCmdRepositoryInterface;
use Procure\Domain\Service\SharedService;
use Webmozart\Assert\Assert;
use ClearingRowRemoved;
use InvalidArgumentException;

/**
 * Clearing Document.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseClearingDoc extends AbstractClearingDoc
{

    private $rowCollection;

    public function addRow(BaseClearingRow $row)
    {
        if (! $row instanceof BaseClearingRow) {
            throw new InvalidArgumentException("input not invalid! AbstractRow");
        }
        $this->getRowCollection()->add($row);
    }

    public function removeRow(PRRow $row, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is posted already! %s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($row, "PRRow not found");

        /**
         *
         * @var PRRowSnapshot $localSnapshot
         * @var PrCmdRepositoryInterface $rep ;
         */

        $this->logInfo("GenericPR will be deleted " . $row->getId());

        $rep = $sharedService->getPostingService()->getCmdRepository();

        $rep->removeRow($this, $row);

        $params = [
            "rowId" => $row->getId(),
            "rowToken" => $row->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new ClearingRowRemoved($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function store(SharedService $sharedService)
    {
        Assert::notNull($sharedService, Translator::translate(sprintf("Shared Service not set! %s", __FUNCTION__)));

        $rep = $sharedService->getPostingService()->getCmdRepository();

        if (! $rep instanceof PrCmdRepositoryInterface) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("PrCmdRepositoryInterface not set! %s", __FUNCTION__)));
        }

        $this->setLogger($sharedService->getLogger());
        $validationService = ValidatorFactory::create($sharedService, true);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $rep->store($this);

        $this->logInfo(\sprintf("PR saved %s", __METHOD__));
        return $this;
    }

    public function createRowFrom(ClearingRowSnapshot $snapshot, CommandOptions $options, SharedService $sharedService, $storeNow = true)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("Clearing Document is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($snapshot, "ClearingRowSnapshot not found");

        $validationService = ValidatorFactory::create($sharedService);

        $snapshot->docType = $this->docType;

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = BaseClearingRow::createFromSnapshot($this, $snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        $this->addRow($row);

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var PRRowSnapshot $localSnapshot
         * @var ClearingCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new ClearingRowAdded($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function updateRowFrom(PRRowSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        Assert::notNull($snapshot, "PRRowSnapshot not found");

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->markAsChange($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $validationService = ValidatorFactory::create($sharedService);

        $row = PRRow::createFromSnapshot($this, $snapshot);
        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        /**
         *
         * @var PRRowSnapshot $localSnapshot
         * @var PrCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        Assert::notNull($localSnapshot, sprintf("Error occured when creating PR Row #%s", $this->getId()));

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new ClearingRowUpdated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function post(CommandOptions $options, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PR is posted!%s", $this->getId()));
        Assert::notNull($options, "command options not found");
        $validationService = ValidatorFactory::create($sharedService);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();
        $this->clearNotification();

        $this->prePost($options, $validationService, $sharedService);
        $this->doPost($options, $validationService, $sharedService);
        $this->afterPost($options, $validationService, $sharedService);

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $params = null;

        $event = new ClearingDocPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    public function makeSnapshot()
    {
        return GenericObjectAssembler::updateAllFieldsFrom(new ClearingDocSnapshot(), $this);
    }

    /*
     * |=============================
     * |Getter and Setter
     * |
     * |=============================
     */
    /**
     *
     * @return mixed
     */
    public function getRowCollection()
    {
        if ($this->rowCollection == null) {
            $this->rowCollection = new ArrayCollection();
        }
        return $this->rowCollection;
    }

    /**
     *
     * @param mixed $rowCollection
     */
    protected function setRowCollection($rowCollection)
    {
        $this->rowCollection = $rowCollection;
    }
}