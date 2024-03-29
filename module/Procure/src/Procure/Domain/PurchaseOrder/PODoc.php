<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\SimpleCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Procure\Application\Command\PO\Options\PoPostOptions;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ProcureTrxStatus;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Domain\Event\Po\PoHeaderUpdated;
use Procure\Domain\Exception\PoCreateException;
use Procure\Domain\Exception\PoInvalidArgumentException;
use Procure\Domain\Exception\PoUpdateException;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\PurchaseOrder\Repository\POCmdRepositoryInterface;
use Procure\Domain\PurchaseOrder\Validator\ValidatorFactory;
use Procure\Domain\QuotationRequest\QRDoc;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\SharedServiceInterface;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Webmozart\Assert\Assert;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class PODoc extends GenericPO
{

    // Addtional attribute.
    private static $instance = null;

    private $grCollection;

    private $apCollection;

    private function __construct()
    {}

    /*
     * |=============================
     * | Methods
     * |
     * |=============================
     */

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::refreshDoc()
     */
    public function refreshDoc()
    {
        // no need, if refreshed.
        if ($this->getRefreshed()) {
            return;
        }

        if ($this->getRowsGenerator() == null) {
            return;
        }

        if (! $this->getRowsGenerator()->valid()) {
            return;
        }

        $rowCollection = new ArrayCollection();

        // refreshing.
        $totalRows = 0;
        $totalPending = 0;

        $totalCompletedGR = 0;
        $totalCompletedAP = 0;
        $totalCompleted = 0;

        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
        $completed = true;

        $totalBilledAmount = 0;

        foreach ($this->getRowsGenerator() as $row) {

            // becasue of yield NULL
            if ($row == null) {
                continue;
            }

            /**
             *
             * @var PORow $row ;
             */
            $row->updateRowStatus(); // Important
            $status = $row->getTransactionStatus();

            $totalRows ++;

            if ($row->getConfirmedGrBalance() <= 0) {
                $totalCompletedGR ++;
            }

            if ($row->getOpenAPQuantity() <= 0 and $row->getOpenAPQuantity() <= 0) {
                $totalCompletedAP ++;
            }

            if ($status == ProcureTrxStatus::COMPLETED) {
                $totalCompleted ++;
            } else {
                $completed = false;
            }

            $totalBilledAmount = $totalBilledAmount + $row->getBilledAmount();
            $netAmount = $netAmount + $row->getNetAmount();

            $taxAmount = $taxAmount + $row->getTaxAmount();
            $grossAmount = $grossAmount + $row->getGrossAmount();

            // add row collection
            $rowCollection->add($row);

            // add row collection @todo: need to removed.
            $this->addRow($row);
        }

        $this->setTransactionStatus(ProcureTrxStatus::UNCOMPLETED);
        if ($completed == true) {
            $this->setTransactionStatus(ProcureTrxStatus::COMPLETED);
        }

        $this->setTotalRows($totalRows);
        $this->setCompletedGRRows($totalCompletedGR);
        $this->setCompletedAPRows($totalCompletedAP);
        $this->setCompletedRows($totalCompleted);

        $this->setBilledAmount($totalBilledAmount);

        $this->setNetAmount($netAmount);
        $this->setTaxAmount($taxAmount);
        $this->setGrossAmount($grossAmount);

        $this->setOpenAPAmount($this->getNetAmount() - $this->getBilledAmount());

        $this->setRowCollection($rowCollection);

        // marked as refreshed.
        $this->refreshed = TRUE;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return GenericSnapshotAssembler::createSnapshotFrom($this, new POSnapshot());
    }

    /**
     *
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PODoc();
        }
        return self::$instance;
    }

    public static function getInstanceFromDB()
    {
        $instance = self::$instance;
        if ($instance == null) {
            $instance = new PODoc();
        }
        $instance->setConstructedFromDB(TRUE);
        return $instance;
    }

    public function cloneAndSave(CommandOptions $options, SharedService $sharedService)
    {
        $rows = $this->getDocRows();
        Assert::notNull($rows, "PO Entity is empty!");
        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        $instance = new self();

        $exculdedProps = [
            "id",
            "uuid",
            "token",
            "docRows",
            "rowIdArray",
            "instance",
            "reversalDoc",
            "grCollection",
            "apCollection"
        ];

        $instance = $this->convertExcludeFieldsTo($instance, $exculdedProps);

        // overwrite.
        $instance->initDoc($options);
        $instance->setDocType(ProcureDocType::PO);
        $instance->setBaseDocId($this->getId());
        $instance->setBaseDocType($this->getDocType());

        $instance->setDocNumber($instance->getDocNumber() . "(copied)");
        $instance->setRemarks(\sprintf("Copied from %s", $instance->getId()));

        $instance->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            $localEntity = PORow::cloneFrom($instance, $r, $options);
            $instance->addRow($localEntity);
            $instance->validateRow($localEntity, $validationService->getRowValidators());
        }

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        $this->clearEvents();
        /**
         *
         * @var PORowSnapshot $localSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($instance);
        Assert::notNull($rootSnapshot, sprintf("Error occured when cloning PO #%s", $instance->getId()));

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PoHeaderCreated($target, $defaultParams, $params);
        $this->addEvent($event);
        return $instance;
    }

    /**
     *
     * @param QRDoc $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public static function createFromQuotation(QRDoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        Assert::isInstanceOf($sourceObj, QRDoc::class, "Quotation Entity is required");

        $rows = $sourceObj->getDocRows();
        Assert::notNull($rows, "Quote Entity is empty!");

        Assert::eq($sourceObj->getDocStatus(), ProcureDocStatus::POSTED, "Quote document is not posted!");
        Assert::notNull($options, "No Options is found");

        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        /**
         *
         * @var PODoc $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(ProcureDocType::PO_FROM_QOUTE); // important.

        $instance->initDoc($options);
        $instance->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            /**
             *
             * @var PORow $r ;
             */

            $localEntity = PORow::createFromQuoteRow($r, $options);
            $instance->addRow($localEntity);
            $instance->validateRow($localEntity, $validationService->getRowValidators());
        }
        return $instance;
    }

    /**
     *
     * @param POSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedServiceInterface $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseOrder\POSnapshot
     */
    public function saveFromQuotation(POSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService)
    {
        Assert::eq($this->getDocStatus(), ProcureDocStatus::DRAFT, sprintf("PO is already posted/closed or being amended! %s", __FUNCTION__));
        Assert::notNull($this->getDocRows(), sprintf("Documment is empty! %s", __FUNCTION__));
        Assert::notNull($options, "command options not found");
        Assert::eq($this->getDocType(), Constants::PROCURE_DOC_TYPE_PO_FROM_QOUTE, sprintf("Doctype is not vadid! %s", __FUNCTION__));

        $validationService = ValidatorFactory::create($sharedService);

        // Entity from Snapshot
        if ($snapshot !== null) {
            $this->setDocCurrency($snapshot->getDocCurrency());
            $this->setDocDate($snapshot->getDocDate());
            $this->setDocNumber($snapshot->getDocNumber());
            $this->setPostingDate($snapshot->getPostingDate());
            $this->setWarehouse($snapshot->getWarehouse());
            $this->setPmtTerm($snapshot->getPmtTerm());
            $this->setRemarks($snapshot->getRemarks());
        }

        $createdDate = new \Datetime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $this->validate($validationService);
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

        /**
         *
         * @var POSnapshot $rootSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();

        $rootSnapshot = $rep->store($this);
        Assert::notNull($rootSnapshot, sprintf("Error occured when saving PO", $this->getId()));
        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\GenericPO::doPost()
     */
    protected function doPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedServiceInterface $sharedService)
    {

        /**
         *
         * @var \Procure\Domain\PurchaseOrder\PORow $row ;
         * @todo: decoup dependency
         * @var PoPostOptions $options ;
         */
        $postedDate = new \Datetime();
        $postedBy = $options->getUserId();
        $this->markAsPosted($postedBy, date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }

            $row->markRowAsPosted($this, $options);
        }

        $this->validate($validationService, true);

        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->getNotification()->errorMessage());
        }
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->post($this, true);
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

    /*
     * |=============================
     * | @deprecated
     * |
     * |=============================
     */

    /**
     *
     * @deprecated
     * @return mixed
     */
    public function getGrCollection()
    {
        if ($this->grCollection == null) {
            $this->grCollection = new SimpleCollection();
            return $this->grCollection;
        }
        return $this->grCollection;
    }

    /**
     *
     * @deprecated
     * @return mixed
     */
    public function getApCollection()
    {
        if ($this->apCollection == null) {
            $this->apCollection = new SimpleCollection();
            return $this->apCollection;
        }
        return $this->apCollection;
    }

    /**
     *
     * @deprecated
     * @param POSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \RuntimeException
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public static function createFrom(POSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notNull($snapshot, "PO snapshot not found");
        Assert::notNull($options, "command options not found");
        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        $snapshot->initDoc($options);
        $snapshot->docType = ProcureDocType::PO;
        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var POSnapshot $rootSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance, false);
        Assert::notNull($rootSnapshot, sprintf("Error occured when creating PO", $instance->getId()));

        $instance->updateIdentityFrom($rootSnapshot);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PoHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);
        return $instance;
    }

    /**
     *
     * @deprecated
     * @param POSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws PoInvalidArgumentException
     * @throws PoCreateException
     * @throws PoUpdateException
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public static function updateFrom(POSnapshot $snapshot, CommandOptions $options, $params, SharedService $sharedService)
    {
        Assert::notNull($snapshot, "PO snapshot not found");
        Assert::notNull($options, "command options not found");
        $validationService = ValidatorFactory::create($sharedService);
        Assert::notNull($validationService, "Validation can not created!");

        $instance = new self();
        $createdDate = new \Datetime();
        $snapshot->markAsChange($options->getUserId(), date_format($createdDate, 'Y-m-d H:i:s'));
        $snapshot->docType = ProcureDocType::PO;

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);
        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var POSnapshot $rootSnapshot
         * @var POCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance, false);
        Assert::notNull($rootSnapshot, sprintf("Error occured when creating PO", $instance->getId()));

        $instance->id = $rootSnapshot->getId();

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PoHeaderUpdated($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    /**
     *
     * @deprecated This should be only call when constructing object from storage.
     *            
     * @param PODetailsSnapshot $snapshot
     * @return void|\Procure\Domain\PurchaseOrder\PODoc
     */
    public static function makeFromDetailsSnapshot(PoSnapshot $snapshot)
    {
        if (! $snapshot instanceof PoSnapshot)
            return;

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
        }
        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @deprecated This should be only call when constructing object from storage.
     *            
     * @param PoSnapshot $snapshot
     * @return void|\Procure\Domain\PurchaseOrder\PODoc
     */
    public static function makeFromSnapshot(PoSnapshot $snapshot)
    {
        if (! $snapshot instanceof PoSnapshot)
            return;

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
            $snapshot->token = $snapshot->uuid;
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @deprecated
     * @param mixed $grList
     */
    public function addGrId($id)
    {
        $collection = $this->getGrCollection();
        $collection->add($id);
    }

    /**
     *
     * @deprecated
     * @param unknown $idArray
     */
    public function addGrArray($idArray)
    {
        if (count($idArray) == null) {
            return;
        }

        foreach ($idArray as $e) {
            $collection = $this->getGrCollection();
            $collection->add($e);
        }
    }

    /**
     *
     * @deprecated
     * @param unknown $idArray
     */
    public function addApArray($idArray)
    {
        if (count($idArray) == null) {
            return;
        }

        foreach ($idArray as $e) {
            $collection = $this->getApCollection();
            $collection->add($e);
        }
    }

    public function addApId($id)
    {
        $collection = $this->getApList();
        $collection->add($id);
    }

    /**
     *
     * @deprecated
     * @param mixed $apList
     */
    public function setApList($apList)
    {
        $this->apList = $apList;
    }
}