<?php
namespace Procure\Domain\QuotationRequest;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Procure\Application\DTO\Qr\QrDTO;
use Procure\Domain\AccountPayable\AbstractAP;
use Procure\Domain\Event\Qr\QrPosted;
use Procure\Domain\Event\Qr\QrRowAdded;
use Procure\Domain\Event\Qr\QrRowUpdated;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\Service\QrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericQR extends AbstractAP
{

    abstract protected function prePost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService);

    abstract protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService);

    abstract protected function afterPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService);

    abstract protected function raiseEvent();

    abstract protected function preReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService);

    abstract protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService);

    abstract protected function afterReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService);

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Procure\Domain\QuotationRequest\GenericQR
     */
    public function validate(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Validators not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("QR Validators not given!");
        }

        // Clear Notification.
        $this->clearNotification();

        $this->validateHeader($headerValidators, $isPosting);

        if ($this->hasErrors()) {
            return $this;
        }

        if (count($this->getDocRows()) == 0) {
            $this->addError("Documment is empty. Please add line!");
            return $this;
        }

        foreach ($this->getDocRows() as $row) {
            $this->validateRow($row, $rowValidators, $isPosting);
        }

        return $this;
    }

    public function deactivateRow(QRRow $row, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {}

    /**
     *
     * @param QRRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param QrPostingService $postingService
     * @throws InvalidOperationException
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\QuotationRequest\QRRowSnapshot
     */
    public function createRowFrom(QRRowSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
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

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $snapshot->docType = $this->docType;

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = QRRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $rowValidators);

        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var QRRowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new OperationFailedException(sprintf("Error occured when creating row #%s", $this->getId()));
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
        $event = new QrRowAdded($target, $defaultParams, $params);

        $this->addEvent($event);

        return $localSnapshot;
    }

    public function updateRowFrom(QRRowSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {
        if ($this->getDocStatus() == Constants::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(sprintf("QR is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("QRRowSnapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->updateSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = QRRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $rowValidators);

        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var QRRowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new OperationFailedException(sprintf("Error occured when creating Quotation Row #%s", $this->getId()));
        }

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $event = new QrRowUpdated($target, $defaultParams, $params);

        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param QrPostingService $postingService
     * @throws InvalidOperationException
     * @throws ValidationFailedException
     * @return \Procure\Domain\QuotationRequest\GenericQR
     */
    public function post(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_DRAFT) {
            throw new InvalidOperationException(Translator::translate(sprintf("Document is already posted/closed or being amended! %s", __FUNCTION__)));
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->prePost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->doPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->afterPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new QrPosted($target, $defaultParams, $params);

        $this->addEvent($event);
        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param QrPostingService $postingService
     * @throws InvalidOperationException
     * @throws ValidationFailedException
     * @return \Procure\Domain\QuotationRequest\GenericQR
     */
    public function reverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(Translator::translate(sprintf("Document is not posted yet! %s", __METHOD__)));
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->preReserve($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->doReverse($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->afterReserve($options, $headerValidators, $rowValidators, $sharedService, $postingService);

        $this->addEvent();
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
     * @param QRRow $row
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     */
    public function validateRow(QRRow $row, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $row instanceof QRRow) {
            throw new InvalidArgumentException("GR Row not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validator not given!");
        }

        $rowValidators->validate($this, $row);

        if ($row->hasErrors()) {
            $this->addErrorArray($row->getErrors());
        }
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new QrDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeHeaderDTO()
    {
        $dto = new QrDTO();
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

                if ($row instanceof QRRow) {
                    $rowDTOList[] = $row->makeDetailsDTO();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param QrPostingService $postingService
     * @throws InvalidArgumentException
     */
    private function _checkParams(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {
        if ($headerValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($rowValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("postingService service not found");
        }
    }
}