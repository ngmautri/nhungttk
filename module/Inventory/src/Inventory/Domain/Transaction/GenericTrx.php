<?php
namespace Inventory\Domain\Transaction;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Domain\Contracts\AutoGeneratedDocInterface;
use Inventory\Domain\Event\Transaction\TrxPosted;
use Inventory\Domain\Event\Transaction\TrxReversed;
use Inventory\Domain\Event\Transaction\TrxRowAdded;
use Inventory\Domain\Event\Transaction\TrxRowUpdated;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxStatus;
use Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface;
use Inventory\Domain\Transaction\Validator\ValidatorFactory;
use Procure\Domain\Contracts\ProcureDocStatus;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericTrx extends BaseDoc
{

    abstract public function specify();

    public function refreshDoc()
    {}

    /**
     *
     * @param int $itemId
     */
    public function getOnhandQuantityOf($itemId)
    {
        if ($itemId == null) {
            return null;
        }

        if ($this->getLazyRowsCollection()->count() == 0) {
            return;
        }

        $onhandQty = 0;
        foreach ($this->getLazyRowSnapshotCollection() as $lazyRowSnapshot) {
            /**
             *
             * @todo
             * @var TrxRowSnapshot $rowSnapshot ;
             */
            $rowSnapshot = $lazyRowSnapshot();

            if ($rowSnapshot->getItem() == $itemId) {
                $onhandQty = $onhandQty + $rowSnapshot->getStockQty();
            }
        }
        return $onhandQty;
    }

    public function updateExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     *
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function updateStatus()
    {
        $this->setTransactionStatus(TrxStatus::UNKNOW);

        if ($this->getMovementFlow() == TrxFlow::WH_TRANSACTION_IN) {

            $this->setTransactionStatus(TrxStatus::GR_PARTIALLY_USED);

            if ($this->getTotalRows() == $this->getUnusedRows()) {
                $this->setTransactionStatus(TrxStatus::GR_UN_USED);
            } elseif ($this->getTotalRows() == $this->getExhaustedRows()) {
                $this->setTransactionStatus(TrxStatus::GR_FULLY_USED);
            }
        }

        return $this;
    }

    /**
     *
     * @param TrxRow $row
     * @param CommandOptions $options
     * @param TrxValidationServiceInterface $validationSerive
     * @param SharedService $sharedService
     */
    public function deactivateRow(TrxRow $row, CommandOptions $options, TrxValidationServiceInterface $validationSerive, SharedService $sharedService)
    {}

    /**
     *
     * @param TrxRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @param boolean $storeNow
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GenericTrx|\Inventory\Domain\Transaction\TrxRowSnapshot
     */
    public function createRowFrom(TrxRowSnapshot $snapshot, CommandOptions $options, SharedService $sharedService, $storeNow = true)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("Trx is already posted %s", $this->getId()));
        Assert::notNull($snapshot, "Row Snapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService);

        $snapshot->flow = $this->getMovementFlow();
        $snapshot->wh = $this->getWarehouse();

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = TrxRow::createFromSnapshot($this, $snapshot);

        // \var_dump($row);
        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->addRow($row);

        if (! $storeNow) {
            return $this;
        }

        // saving to storage now.

        $this->clearEvents();

        /**
         *
         * @var TrxRowSnapshot $localSnapshot ;
         * @var TrxCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this;

        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $event = new TrxRowAdded($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new TrxSnapshot());
    }

    /**
     *
     * @param TrxRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return \Inventory\Domain\Transaction\TrxRowSnapshot
     */
    public function updateRowFrom(TrxRowSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notEq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("Trx is already posted %s", $this->getId()));
        Assert::notNull($snapshot, "Row Snapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = TrxRow::createFromSnapshot($this, $snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var TrxRowSnapshot $localSnapshot
         */
        $localSnapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->storeRow($this, $row);

        // $target = $this->makeSnapshot(); //

        $target = null;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $event = new TrxRowUpdated($target, $defaultParams, $params);

        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function post(CommandOptions $options, SharedService $sharedService)
    {
        if ($this instanceof AutoGeneratedDocInterface) {
            $f = Translator::translate("Manual posting is not allowed on this document type! %s");
            throw new \InvalidArgumentException(Translator::translate(sprintf($f, $this->getDocType())));
        }

        Assert::eq($this->getDocStatus(), ProcureDocStatus::DRAFT, sprintf("Document is already posted/closed or being amended! %s", $this->getId()));
        Assert::notNull($options, "Options not founds");

        $rep = $sharedService->getPostingService()->getCmdRepository();

        if (! $rep instanceof TrxCmdRepositoryInterface) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("TrxCmdRepositoryInterface not set! %s", __FUNCTION__)));
        }

        $this->setLogger($sharedService->getLogger());
        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService, true);

        $this->validate($validationService, true);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

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

        $event = new TrxPosted($target, $defaultParams, $params);

        $this->addEvent($event);
        $this->logInfo(\sprintf("Trx Posted %s", __METHOD__));
        return $this;
    }

    /**
     *
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function store(SharedService $sharedService)
    {
        Assert::eq($this->getMovementFlow(), TrxFlow::WH_TRANSACTION_OUT, Translator::translate(sprintf("Flow in not correct%s", __FUNCTION__)));
        Assert::notNull($sharedService, Translator::translate(sprintf("Shared Service not set! %s", __FUNCTION__)));

        $rep = $sharedService->getPostingService()->getCmdRepository();

        if (! $rep instanceof TrxCmdRepositoryInterface) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("TrxCmdRepositoryInterface not set! %s", __FUNCTION__)));
        }

        $this->setLogger($sharedService->getLogger());
        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService, true);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $rep->store($this);

        $this->logInfo(\sprintf("Trx saved %s", __METHOD__));
        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function saveAfterCalculationCost(SharedService $sharedService)
    {
        Assert::eq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("Document should be posted! %s", $this->getId()));
        Assert::notNull($sharedService, "Shared Service not founds");

        if ($this->getMovementFlow() != TrxFlow::WH_TRANSACTION_OUT) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("Flow in not correct%s", __FUNCTION__)));
        }

        $rep = $sharedService->getPostingService()->getCmdRepository();

        if (! $rep instanceof TrxCmdRepositoryInterface) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("TrxCmdRepositoryInterface not set! %s", __FUNCTION__)));
        }

        $this->setLogger($sharedService->getLogger());
        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService, true);

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $rep->storeWithoutUpdateVersion($this);

        $this->logInfo(\sprintf("Trx saved after calculation cost%s", __METHOD__));
        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param TrxValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function reverse(CommandOptions $options, SharedService $sharedService)
    {
        if ($this instanceof AutoGeneratedDocInterface) {
            $f = Translator::translate("Manual posting is not allowed on this document type! %s");
            throw new \InvalidArgumentException(Translator::translate(sprintf($f, $this->getDocType())));
        }

        Assert::eq($this->getDocStatus(), ProcureDocStatus::POSTED, sprintf("Document should be posted! %s", $this->getId()));

        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService, true);

        $this->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->preReserve($options, $validationService, $sharedService);
        $this->doReverse($options, $validationService, $sharedService);
        $this->afterReserve($options, $validationService, $sharedService);

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new TrxReversed($target, $defaultParams, $params);

        $this->addEvent($event);
        return $this;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new TrxDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeHeaderDTO()
    {
        $dto = new TrxDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @param object $dto
     * @return NULL|object
     */
    public function makeDTOForGrid()
    {
        $dto = new TrxSnapshot();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        $rowDTOList = [];
        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof TrxRow) {
                    $rowDTOList[] = $row->makeDetailsDTO();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::afterPost()
     */
    protected function afterPost(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::afterReserve()
     */
    protected function afterReserve(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::doPost()
     */
    protected function doPost(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::doReverse()
     */
    protected function doReverse(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::prePost()
     */
    protected function prePost(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::preReserve()
     */
    protected function preReserve(\Application\Domain\Shared\Command\CommandOptions $options, \Procure\Domain\Service\Contracts\ValidationServiceInterface $validationService, \Procure\Domain\Service\Contracts\SharedServiceInterface $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::raiseEvent()
     */
    protected function raiseEvent()
    {
        // TODO Auto-generated method stub
    }
}