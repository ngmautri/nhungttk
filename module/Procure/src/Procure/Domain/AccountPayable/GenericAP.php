<?php
namespace Procure\Domain\AccountPayable;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Procure\Application\DTO\Ap\ApDetailsDTO;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Domain\Event\Ap\ApReversed;
use Procure\Domain\Event\Ap\ApRowAdded;
use Procure\Domain\Event\Ap\ApRowUpdated;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericAP extends AbstractAP
{

    abstract protected function prePost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function doPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function afterPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function raiseEvent();

    abstract protected function preReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function doReverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function afterReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    /**
     *
     * @param ValidationServiceInterface $validationService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\GenericAP
     */
    public function validate(ValidationServiceInterface $validationService, $isPosting = false)
    {
        if ($validationService == null) {
            throw new InvalidArgumentException("Validation service not given!");
        }

        if (! $validationService->getHeaderValidators() instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Rows Validators not given!");
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

    public function deactivateRow(APRow $row, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidOperationException
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\AccountPayable\APRowSnapshot
     */
    public function createRowFrom(APRowSnapshot $snapshot, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if ($this->getDocStatus() == Constants::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(sprintf("AP is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("Row Snapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $this->_checkParams($validationService, $sharedService);

        $snapshot->docType = $this->docType;

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = APRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var APRowSnapshot $localSnapshot
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new \RuntimeException(sprintf("Error occured when creating row #%s", $this->getId()));
        }

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

        $event = new ApRowAdded($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidOperationException
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\AccountPayable\APRowSnapshot
     */
    public function updateRowFrom(APRowSnapshot $snapshot, CommandOptions $options, $params, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if ($this->getDocStatus() == Constants::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("APRowSnapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $this->_checkParams($validationService, $sharedService);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = APRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var APRowSnapshot $localSnapshot
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new \RuntimeException(sprintf("Error occured when creating GR Row #%s", $this->getId()));
        }

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new ApRowUpdated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    protected function refreshRows(APSnapshot $snapshot)
    {
        foreach ($this->getDocRows() as $row) {
            /**
             *
             * @var APRow $row ;
             */
            $row->refreshRowsFromNewHeaderSnapshot($snapshot);
        }
    }

    /**
     *
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidOperationException
     * @throws \RuntimeException
     * @return \Procure\Domain\AccountPayable\GenericAP
     */
    public function post(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_DRAFT) {
            throw new InvalidOperationException(Translator::translate(sprintf("Document is already posted/closed or being amended! %s", __FUNCTION__)));
        }

        $this->_checkParams($validationService, $sharedService);

        $this->validate($validationService);

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
        $params = [
            "message" => \sprintf("AP %s posted successfully!", $this->getId())
        ];
        $event = new ApPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidOperationException
     * @throws ValidationFailedException
     * @return \Procure\Domain\AccountPayable\GenericAP
     */
    public function reverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(Translator::translate(sprintf("Document is not posted yet! %s", __METHOD__)));
        }

        $this->_checkParams($validationService, $sharedService);

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

        $params = [
            "message" => \sprintf("AP %s reversed successfully!", $this->getId())
        ];

        $event = new ApReversed($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
    }

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param boolean $isPosting
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
     * @param APRow $row
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     */
    public function validateRow(APRow $row, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $row instanceof APRow) {
            throw new InvalidArgumentException("AP Row not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validator not given!");
        }

        $rowValidators->validate($this, $row);

        $row->calculate(); // important

        if ($row->hasErrors()) {
            $this->addErrorArray($row->getErrors());
            return;
        }
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new ApDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeHeaderDTO()
    {
        $dto = new ApDetailsDTO();
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

                if ($row instanceof APRow) {
                    $rowDTOList[] = $row->makeDetailsDTO();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }

    /**
     *
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     */
    private function _checkParams(ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if ($validationService == null) {
            throw new InvalidArgumentException('Validation service not found!');
        }

        if ($sharedService == null) {
            throw new InvalidArgumentException('SharedService service not found');
        }
    }
}