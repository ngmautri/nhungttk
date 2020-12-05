<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ProcureGoodsFlow;
use Procure\Domain\Contracts\ProcureTrxStatus;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\Event\Gr\GrReversed;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;
use Procure\Domain\GoodsReceipt\Validator\ValidatorFactory;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\SharedServiceInterface;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRDoc extends GenericGR
{

    public function specify()
    {
        $this->flow = ProcureGoodsFlow::IN;
        $this->docType = ProcureDocType::GR;
    }

    // =====================
    private function __construct()
    {}

    /**
     *
     * @param \Procure\Domain\GoodsReceipt\GRDoc $instance
     */
    protected static function setInstance($instance)
    {
        GRDoc::$instance = $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new GRSnapshot());
    }

    /**
     *
     * @param PODoc $sourceObj
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function createFromPo(PODoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        if (! $sourceObj instanceof PODoc) {
            throw new \InvalidArgumentException("PO Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new \InvalidArgumentException("PO Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::POSTED) {
            throw new \RuntimeException("PO document is not posted!");
        }

        if ($sourceObj->getTransactionStatus() == ProcureTrxStatus::COMPLETED) {
            throw new \RuntimeException("GR is completed!");
        }

        if ($options == null) {
            throw new \InvalidArgumentException("No Options is found");
        }

        $validationService = ValidatorFactory::create($this->getDocType(), $sharedService);

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(ProcureDocType::GR_FROM_PO); // important.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            /**
             *
             * @var \Procure\Domain\PurchaseOrder\PORow $r ;
             */

            // ignore completed row;
            if ($r->getOpenGrBalance() == 0) {
                continue;
            }

            $grRow = GrRow::createFromPoRow($r, $options);
            // echo sprintf("\n %s, PoRowId %s, %s" , $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
            $instance->addRow($grRow);

            $instance->validateRow($grRow, $validationService->getRowValidators());
        }
        return $instance;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function copyFromAP(APDoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        if (! $sourceObj instanceof APDoc) {
            throw new \InvalidArgumentException("AP Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new \InvalidArgumentException("AP Entity is empty!");
        }
        if ($options == null) {
            throw new \InvalidArgumentException("No Options is found");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::POSTED) {
            throw new \RuntimeException("AP document is not posted!");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(Constants::PROCURE_DOC_TYPE_GR_FROM_INVOICE); // important.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->validateHeader($headerValidators);

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */

            $grRow = GrRow::copyFromApRow($r, $options);
            // echo sprintf("\n %s, PoRowId %s, %s" , $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
            $instance->addRow($grRow);

            $instance->validateRow($grRow, $rowValidators);
        }
        return $instance;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function postCopyFromAP(APDoc $sourceObj, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if (! $sourceObj instanceof APDoc) {
            throw new \InvalidArgumentException("AP Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new \InvalidArgumentException("AP Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::POSTED) {
            throw new \RuntimeException("AP document is not posted yet!");
        }

        if ($options == null) {
            throw new \InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new self();

        $instance->_checkParams($validationService, $sharedService);

        // also row validation needed.
        if ($validationService->getRowValidators() == null) {
            throw new \InvalidArgumentException("Rows validators not found");
        }

        $instance = $sourceObj->convertTo($instance);

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        // overwrite.
        $instance->setDocType(Constants::PROCURE_DOC_TYPE_GR_FROM_INVOICE); // important.
        $instance->markAsPosted($createdBy, $sourceObj->getPostingDate());

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */

            $grRow = GrRow::copyFromApRow($instance, $r, $options);
            $grRow->markAsPosted($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
            $instance->addRow($grRow);
        }

        $instance->setInvoiceNo($sourceObj->getDocNumber());
        $instance->setRemarks(\sprintf("[Auto.] Ref.AP %s", $sourceObj->getSysNumber()));

        $validationService = ValidatorFactory::create($instance->getDocType(), $sharedService);
        $instance->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        $instance->clearEvents();

        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($instance, true);

        if (! $snapshot instanceof GRSnapshot) {
            throw new \RuntimeException(sprintf("Error orcured when creating GR #%s", $instance->getId()));
        }

        $instance->setId($snapshot->getId());
        $instance->setToken($snapshot->getToken());

        $target = $snapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $event = new GrPosted($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function postCopyFromAPRerveral(APDoc $sourceObj, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if (! $sourceObj instanceof APDoc) {
            throw new \InvalidArgumentException("AP Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new \InvalidArgumentException("AP Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::REVERSED) {
            throw new \RuntimeException(\sprintf("AP document status is not valid for this operation! %s", $sourceObj->getDocStatus()));
        }

        if ($options == null) {
            throw new \InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new self();
        $instance->_checkParams($validationService, $sharedService);
        // also row validation needed.
        if ($validationService->getRowValidators() == null) {
            throw new \InvalidArgumentException("Rows validators not found");
        }

        $instance = $sourceObj->convertTo($instance);

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        // overwrite.
        $instance->setDocType(ProcureDocType::GR_FROM_INVOICE); // important.
        $instance->markAsReversed($createdBy, $sourceObj->getReversalDate());

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */

            $grRow = GrRow::copyFromApRow($instance, $r, $options);
            $grRow->markAsReversed($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
            $instance->addRow($grRow);
        }
        $instance->setInvoiceNo($sourceObj->getDocNumber());
        $instance->setRemarks(\sprintf("[Auto.] Ref.AP Reversal %s", $sourceObj->getId()));

        $instance->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        $instance->clearEvents();

        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($instance, true);

        if (! $snapshot instanceof GRSnapshot) {
            throw new \RuntimeException(sprintf("Error orcured when creating GR from AP reversal #%s", $sourceObj->getId()));
        }

        $target = $snapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $event = new GrReversed($target, $defaultParams, $params);
        $instance->addEvent($event);

        $instance->setId($snapshot->getId());
        $instance->setToken($snapshot->getToken());
        return $instance;
    }

    /**
     *
     * @param CommandOptions $options
     * @param GRSnapshot $snapshot
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param GrPostingService $postingService
     * @throws InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws OperationFailedException
     */
    public function saveFromPO(GRSnapshot $snapshot, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if (! $this->getDocStatus() == ProcureDocStatus::DRAFT) {
            throw new InvalidArgumentException(sprintf("PO is already posted/closed or being amended! %s", $this->getId()));
        }

        if ($this->getDocRows() == null) {
            throw new InvalidArgumentException(sprintf("Documment is empty! %s", $this->getId()));
        }

        if (! $this->getDocType() == Constants::PROCURE_DOC_TYPE_GR_FROM_PO) {
            throw new InvalidArgumentException(sprintf("Doctype is not vadid! %s", $this->getDocType()));
        }

        $this->_checkInputParams($snapshot, $headerValidators, $sharedService, $postingService);

        if ($rowValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Comnand Options not found!");
        }

        // Update Good Receipt Date and WH
        if ($snapshot !== null) {
            $this->setGrDate($snapshot->getGrDate());
            $this->setWarehouse($snapshot->getWarehouse());
        }

        $createdDate = new \Datetime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new OperationFailedException($this->getErrorMessage());
        }

        $this->clearEvents();
        $rootSnapshot = $postingService->getCmdRepository()->store($this);

        return $rootSnapshot;
    }

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function getInstance()
    {
        new self();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::doPost()
     */
    protected function doPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {
        /**
         *
         * @var GRRow $row ;
         */
        $postedDate = new \Datetime();

        $this->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }

            $row->markRowAsPosted($this, $options);
        }

        $this->validate($validationService, true);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var GRRowSnapshot $localSnapshot
         * @var GrCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->post($this);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::doReverse()
     */
    protected function doReverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        /**
         *
         * @var GRRow $row ;
         */
        $postedDate = new \Datetime();

        $this->markAsReversed($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }

            $row->markAsReversed($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));
        }

        $this->validate($validationService, true);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        /**
         *
         * @var GRRowSnapshot $localSnapshot
         * @var GrCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->post($this);
    }
}