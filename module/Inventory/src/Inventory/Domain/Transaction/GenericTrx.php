<?php
namespace Inventory\Domain\Transaction;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Domain\Event\Transaction\TrxPosted;
use Inventory\Domain\Event\Transaction\TrxReversed;
use Inventory\Domain\Event\Transaction\TrxRowAdded;
use Inventory\Domain\Event\Transaction\TrxRowUpdated;
use Inventory\Domain\Exception\InvalidOperationException;
use Inventory\Domain\Exception\OperationFailedException;
use Inventory\Domain\Exception\ValidationFailedException;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\PostingServiceInterface;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface;
use Inventory\Domain\Transaction\Validator\ValidatorFactory;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;
use Procure\Domain\Shared\ProcureDocStatus;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericTrx extends BaseDoc
{

    abstract public function specify();

    public function updateExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    abstract protected function prePost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function doPost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function afterPost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function preReserve(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function doReverse(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function afterReserve(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService);

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function validate(TrxValidationServiceInterface $validationService, $isPosting = false)
    {
        if ($validationService == null) {
            throw new InvalidArgumentException("Validation service not given!");
        }

        if (! $validationService->getHeaderValidators() instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        // Clear Notification.
        $this->clearNotification();

        $this->validateHeader($validationService->getHeaderValidators(), $isPosting);

        if ($this->hasErrors()) {
            return $this;
        }

        if (count($this->getDocRows()) == 0) {
            $this->addError("Documment is empty. Please add line!");
            return $this;
        }

        foreach ($this->getDocRows() as $row) {
            $this->validateRow($row, $validationService->getRowValidators(), $isPosting);
        }

        return $this;
    }

    /**
     *
     * @param TrxRow $row
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param PostingServiceInterface $postingService
     */
    public function deactivateRow(TrxRow $row, CommandOptions $options, TrxValidationServiceInterface $validationSerive, SharedService $sharedService)
    {}

    /**
     *
     * @param TrxRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param PostingServiceInterface $postingService
     * @throws InvalidOperationException
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Inventory\Domain\Transaction\TrxRowSnapshot
     */
    public function createRowFrom(TrxRowSnapshot $snapshot, CommandOptions $options, SharedService $sharedService, $storeNow = true)
    {
        if ($this->getDocStatus() == Constants::DOC_STATUS_POSTED) {
            throw new \InvalidArgumentException(sprintf("PR is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("Row Snapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService);

        $this->_checkParams($validationService, $sharedService);

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validators not given!");
        }

        $snapshot->flow = $this->getMovementFlow();
        $snapshot->wh = $this->getWarehouse();

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = TrxRow::makeFromSnapshot($snapshot);

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

        $this->recordedEvents = array();

        /**
         *
         * @var TrxRowSnapshot $localSnapshot ;
         * @var TrxCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new RuntimeException(sprintf("Error occured when creating row #%s", $this->getId()));
        }

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
     * @throws InvalidOperationException
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Inventory\Domain\Transaction\TrxRowSnapshot
     */
    public function updateRowFrom(TrxRowSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        if ($this->getDocStatus() == Constants::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(sprintf("Trx is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("TrxRowSnapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService);

        $this->_checkParams($validationService, $sharedService);

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validators not given!");
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = TrxRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var TrxRowSnapshot $localSnapshot
         */
        $localSnapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new RuntimeException(sprintf("Error occured when updatting Trx row #%s", $this->getId()));
        }

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
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param PostingServiceInterface $postingService
     * @throws InvalidOperationException
     * @throws ValidationFailedException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function post(CommandOptions $options, SharedService $sharedService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_DRAFT) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("Document is already posted/closed or being amended! %s", __FUNCTION__)));
        }

        if ($sharedService == null) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("Shared Service not set! %s", __FUNCTION__)));
        }

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
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function store(SharedService $sharedService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_DRAFT) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("Document is already posted/closed or being amended! %s", __FUNCTION__)));
        }

        if ($sharedService == null) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("Shared Service not set! %s", __FUNCTION__)));
        }

        $rep = $sharedService->getPostingService()->getCmdRepository();

        if (! $rep instanceof TrxCmdRepositoryInterface) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("TrxCmdRepositoryInterface not set! %s", __FUNCTION__)));
        }

        $this->setLogger($sharedService->getLogger());

        $validationService = ValidatorFactory::create($this->getMovementType(), $sharedService, false);

        $this->validate($validationService, false);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();
        $rep->store($this);

        $this->logInfo(\sprintf("WH Trx %s saved! %s", $this->getId(), __METHOD__));
        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param PostingServiceInterface $postingService
     * @throws InvalidOperationException
     * @throws ValidationFailedException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public function reverse(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(Translator::translate(sprintf("Document is not posted yet! %s", __METHOD__)));
        }

        $this->_checkParams($validationService, $sharedService);

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validators not given!");
        }

        $this->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->getErrorMessage());
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
     * @param HeaderValidatorCollection $headerValidators
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     */
    public function validateHeader(HeaderValidatorCollection $headerValidators, $isPosting = false)
    {
        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Validators not given!");
        }

        $headerValidators->validate($this);
    }

    /**
     *
     * @param TrxRow $row
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     */
    public function validateRow(TrxRow $row, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $row instanceof TrxRow) {
            throw new InvalidArgumentException("GR Row not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validator not given!");
        }

        $rowValidators->validate($this, $row);

        if ($row->hasErrors()) {
            $this->addErrorArray($row->getErrors());
        }

        $row->calculate(); //
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
    public function makeDTOForGrid($dto)
    {
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
     * @param TrxValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     */
    private function _checkParams(TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if ($validationService == null) {
            throw new InvalidArgumentException('Validation service not found!');
        }

        if (! $validationService->getHeaderValidators() instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        if ($sharedService == null) {
            throw new InvalidArgumentException('SharedService service not found');
        }
    }
}