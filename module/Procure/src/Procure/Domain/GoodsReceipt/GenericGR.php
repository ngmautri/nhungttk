<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\SimpleCollection;
use Application\Domain\Util\Translator;
use Procure\Application\DTO\Gr\GrDetailsDTO;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\Event\Gr\GrReversed;
use Procure\Domain\Event\Gr\GrRowAdded;
use Procure\Domain\Event\Gr\GrRowUpdated;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\Exception\Gr\GrInvalidOperationException;
use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Ramsey\Uuid\Uuid;
use Procure\Domain\GoodsReceipt\Validator\ValidatorFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericGR extends BaseDoc
{

    abstract protected function prePost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function doPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function afterPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function raiseEvent();

    abstract protected function preReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function doReverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    abstract protected function afterReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService);

    public function slipByWarehouse()
    {
        $results = [];

        if ($this->getTargetWhList() == null) {
            return null;
        }
        if (count($this->getTargetWhList() > 1)) {
            foreach ($this->getTargetWhList() as $wh) {
                $doc = new self();
            }
        }
    }

    public function slipByDepartment()
    {
        $results = new SimpleCollection();

        if ($this->getTargetDepartmentList() == null) {
            return null;
        }

        foreach ($this->getDocRows() as $row) {
            /**
             *
             * @var GRRow $row ;
             */
            $dept = $row->getPrDepartment();

            if ($dept == null) {
                $dept = - 1;
            }
            $results->addChild($dept, $row);
        }

        return $results;
    }

    /**
     *
     * @param ValidationServiceInterface $validationService
     * @param boolean $isPosting
     * @throws \InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GenericGR
     */
    public function validate(ValidationServiceInterface $validationService, $isPosting = false)
    {
        if ($validationService == null) {
            throw new \InvalidArgumentException("GR Validators not given!");
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

    public function deactivateRow(GRRow $row, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    public function createRowFrom(GRRowSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if ($this->getDocStatus() == ProcureDocStatus::POSTED) {
            throw new \InvalidArgumentException(sprintf("GR is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new \InvalidArgumentException("PORowSnapshot not found");
        }

        $createdDate = new \Datetime();
        $snapshot->createdOn = date_format($createdDate, 'Y-m-d H:i:s');
        $snapshot->quantity = $snapshot->docQuantity;
        $snapshot->revisionNo = 0;

        if ($snapshot->token == null) {
            $snapshot->token = Uuid::uuid4()->toString();
            $snapshot->uuid = $snapshot->getToken();
        }

        $snapshot->docStatus = ProcureDocStatus::DOC_STATUS_DRAFT;
        $snapshot->isActive = 1;
        $snapshot->docType = $this->docType;
        $snapshot->isDraft = 1;
        $snapshot->unitPrice = $snapshot->getDocUnitPrice();
        $snapshot->unit = $snapshot->getDocUnit();

        $row = GRRow::makeFromSnapshot($snapshot);

        $validationService = ValidatorFactory::create($this->getDocType(), $sharedService);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var GRRowSnapshot $localSnapshot
         * @var GrCmdRepositoryInterface $rep
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
        $defaultParams->setTargetRrevisionNo($this());
        $defaultParams->setUserId($options->getUserId());

        $event = new GrRowAdded($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param GRRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRRowSnapshot
     */
    public function updateRowFrom(GRRowSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        if ($this->getDocStatus() == ProcureDocStatus::POSTED) {
            throw new \InvalidArgumentException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new \InvalidArgumentException("PORowSnapshot not found");
        }

        $createdDate = new \Datetime();
        $snapshot->lastchangeOn = date_format($createdDate, 'Y-m-d H:i:s');
        $snapshot->quantity = $snapshot->docQuantity;

        if ($snapshot->token == null) {
            $snapshot->token = Uuid::uuid4()->toString();
        }

        $snapshot->docType = $this->docType;
        $snapshot->isDraft = 1;
        $snapshot->unitPrice = $snapshot->getDocUnitPrice();
        $snapshot->unit = $snapshot->getDocUnit();

        $row = GrRow::makeFromSnapshot($snapshot);

        $validationService = ValidatorFactory::create($this->getDocType(), $sharedService);

        $this->validateRow($row, $validationService->getRowValidators());

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var GRRowSnapshot $localSnapshot
         * @var GrCmdRepositoryInterface $rep
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
        $defaultParams->setTargetRrevisionNo($this());
        $defaultParams->setUserId($options->getUserId());

        $event = new GrRowUpdated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GenericGR
     */
    public function post(CommandOptions $options, SharedService $sharedService)
    {
        if ($this->getDocStatus() != ProcureDocStatus::DRAFT) {
            throw new \InvalidArgumentException(Translator::translate(sprintf("Document is already posted/closed or being amended! %s", __METHOD__)));
        }

        $validationService = ValidatorFactory::create($this->getDocType(), $sharedService);

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
        $defaultParams->setUserId($options->getUserId());
        $params = [];

        $event = new GrPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws GrInvalidOperationException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GenericGR
     */
    public function reverse(CommandOptions $options, SharedService $sharedService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new GrInvalidOperationException(Translator::translate(sprintf("Document is not posted yet! %s", __METHOD__)));
        }

        $validationService = ValidatorFactory::create($this->getDocType(), $sharedService);
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
        $defaultParams->setTargetRrevisionNo($this());
        $defaultParams->setUserId($options->getUserId());
        $params = [];
        $event = new GrReversed($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
    }

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param boolean $isPosting
     * @throws \InvalidArgumentException
     */
    public function validateHeader(HeaderValidatorCollection $headerValidators, $isPosting = false)
    {
        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new \InvalidArgumentException("GR Validators not given!");
        }

        $headerValidators->validate($this);
    }

    /**
     *
     * @param GrRow $row
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws GrInvalidArgumentException
     */
    public function validateRow(GrRow $row, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $row instanceof GrRow) {
            throw new \InvalidArgumentException("GR Row not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new \InvalidArgumentException("Row Validator not given!");
        }

        $rowValidators->validate($this, $row);

        if ($row->hasErrors()) {
            $this->addErrorArray($row->getErrors());
        } else {
            $row->refresh();
        }
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new GrDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeHeaderDTO()
    {
        $dto = new GrDetailsDTO();
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

                if ($row instanceof GRRow) {
                    $rowDTOList[] = $row->makeDetailsDTO();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }
}
