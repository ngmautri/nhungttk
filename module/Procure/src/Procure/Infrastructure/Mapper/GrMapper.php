<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\NmtProcureGr;
use Application\Entity\NmtProcureGrRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\APInvoice\APDocRowDetailsSnapshot;
use Procure\Domain\GoodsReceipt\GRDetailsSnapshot;
use Procure\Domain\GoodsReceipt\GRRowDetailsSnapshot;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\PurchaseOrder\PORowDetailsSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PORowDetailsSnapshot $sourceSnapShot
     * @param APDocRowDetailsSnapshot $targetSnapShot
     */
    public static function mapPOtoAPRowDetail(EntityManager $doctrineEM, PORowDetailsSnapshot $sourceSnapShot, APDocRowDetailsSnapshot $targetSnapShot)
    {
        if ($sourceSnapShot == null || $targetSnapShot == null || $targetSnapShot == null) {
            return null;
        }

        // $targetSnapShot->id = $sourceSnapShot-;
        $targetSnapShot->rowNumber = $sourceSnapShot->rowNumber;
        // $targetSnapShot->token = $sourceSnapShot-;
        $targetSnapShot->quantity = $sourceSnapShot->openAPQuantity;
        $targetSnapShot->unitPrice = $sourceSnapShot->docUnitPrice;

        $targetSnapShot->netAmount = $sourceSnapShot->netAmount;
        $targetSnapShot->unit = $sourceSnapShot->docUnit;
        $targetSnapShot->itemUnit = $sourceSnapShot->itemUnit;

        $targetSnapShot->conversionFactor = $sourceSnapShot->conversionFactor;

        // $targetSnapShot->converstionText = $sourceSnapShot-;
        $targetSnapShot->taxRate = $sourceSnapShot->taxRate;
        $targetSnapShot->remarks = $sourceSnapShot->remarks;

        $targetSnapShot->isActive = $sourceSnapShot->isActive;
        // $targetSnapShot->createdOn = $sourceSnapShot-;
        // $targetSnapShot->lastchangeOn = $sourceSnapShot-;
        // $targetSnapShot->currentState = $sourceSnapShot-;
        $targetSnapShot->vendorItemCode = $sourceSnapShot->vendorItemCode;
        $targetSnapShot->traceStock = $sourceSnapShot->traceStock;

        $targetSnapShot->grossAmount = $sourceSnapShot->grossAmount;
        $targetSnapShot->taxAmount = $sourceSnapShot->taxAmount;
        $targetSnapShot->faRemarks = $sourceSnapShot->faRemarks;

        // $targetSnapShot->rowIdentifer = $sourceSnapShot-;
        $targetSnapShot->discountRate = $sourceSnapShot->discountRate;

        // $targetSnapShot->revisionNo = $sourceSnapShot-;
        // $targetSnapShot->localUnitPrice = $sourceSnapShot->uni;

        $targetSnapShot->docUnitPrice = $sourceSnapShot->docUnitPrice;
        $targetSnapShot->exwUnitPrice = $sourceSnapShot->exwUnitPrice;
        // $targetSnapShot->exwCurrency = $sourceSnapShot->;

        // $targetSnapShot->localNetAmount = $sourceSnapShot->netAmount;
        // $targetSnapShot->localGrossAmount = $sourceSnapShot-;

        // $targetSnapShot->docStatus = $sourceSnapShot-;
        // $targetSnapShot->workflowStatus = $sourceSnapShot-;
        // $targetSnapShot->transactionType = $sourceSnapShot-;
        // $targetSnapShot->isDraft = $sourceSnapShot-;
        // $targetSnapShot->isPosted = $sourceSnapShot-;
        // $targetSnapShot->transactionStatus = $sourceSnapShot-;

        // $targetSnapShot->totalExwPrice = $sourceSnapShot-;
        // $targetSnapShot->convertFactorPurchase = $sourceSnapShot-;
        // $targetSnapShot->convertedPurchaseQuantity = $sourceSnapShot-;
        // $targetSnapShot->convertedStockQuantity = $sourceSnapShot-;
        // $targetSnapShot->convertedStockUnitPrice = $sourceSnapShot-;
        // $targetSnapShot->convertedStandardQuantity = $sourceSnapShot-;
        // $targetSnapShot->convertedStandardUnitPrice = $sourceSnapShot-;
        // $targetSnapShot->docQuantity = $sourceSnapShot-;
        $targetSnapShot->docUnit = $sourceSnapShot->docUnit;
        // $targetSnapShot->convertedPurchaseUnitPrice = $sourceSnapShot-;
        // $targetSnapShot->isReversed = $sourceSnapShot-;
        // $targetSnapShot->reversalDate = $sourceSnapShot-;
        // $targetSnapShot->reversalReason = $sourceSnapShot-;
        // $targetSnapShot->reversalDoc = $sourceSnapShot-;
        // $targetSnapShot->isReversable = $sourceSnapShot-;
        // $targetSnapShot->docType = $sourceSnapShot-;
        // $targetSnapShot->descriptionText = $sourceSnapShot-;
        // $targetSnapShot->vendorItemName = $sourceSnapShot-;
        // $targetSnapShot->reversalBlocked = $sourceSnapShot-;
        // $targetSnapShot->invoice = $sourceSnapShot-;
        // $targetSnapShot->glAccount = $sourceSnapShot-;
        // $targetSnapShot->costCenter = $sourceSnapShot-;
        $targetSnapShot->docUom = $sourceSnapShot->docUom;
        $targetSnapShot->prRow = $sourceSnapShot->prRow;

        // $targetSnapShot->createdBy = $sourceSnapShot-;
        $targetSnapShot->warehouse = $sourceSnapShot->warehouse;
        // $targetSnapShot->lastchangeBy = $sourceSnapShot-;
        $targetSnapShot->poRow = $sourceSnapShot->id;
        $targetSnapShot->item = $sourceSnapShot->item;
        // $targetSnapShot->grRow = $sourceSnapShot->;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param GRSnapshot $snapshot
     * @param NmtProcureGr $entity
     * @return NULL|\Application\Entity\NmtProcureGr
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, GRSnapshot $snapshot, NmtProcureGr $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setId($snapshot->id);
        $entity->setToken($snapshot->token);
        $entity->setVendorName($snapshot->vendorName);
        $entity->setInvoiceNo($snapshot->invoiceNo);
        $entity->setInvoiceDate($snapshot->invoiceDate);
        $entity->setCurrencyIso3($snapshot->currencyIso3);
        $entity->setExchangeRate($snapshot->exchangeRate);
        $entity->setRemarks($snapshot->remarks);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setIsActive($snapshot->isActive);
        $entity->setTrxType($snapshot->trxType);
        $entity->setSapDoc($snapshot->sapDoc);
        $entity->setContractNo($snapshot->contractNo);
        $entity->setContractDate($snapshot->contractDate);
        $entity->setQuotationNo($snapshot->quotationNo);
        $entity->setQuotationDate($snapshot->quotationDate);
        $entity->setSysNumber($snapshot->sysNumber);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setDeliveryMode($snapshot->deliveryMode);
        $entity->setIncoterm($snapshot->incoterm);
        $entity->setIncotermPlace($snapshot->incotermPlace);
        $entity->setPaymentTerm($snapshot->paymentTerm);
        $entity->setPaymentMethod($snapshot->paymentMethod);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setReversalDoc($snapshot->reversalDoc);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setDocType($snapshot->docType);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setUuid($snapshot->uuid);
        $entity->setDocVersion($snapshot->docVersion);

        // DATE MAPPING
        // ++++++++++++++++++++++++++++++++

        // $entity->setCreatedOn($snapshot->createdOn);
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        // $entity->setLastchangeOn($snapshot->lastchangeOn);
        if ($snapshot->lastchangeOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->lastchangeOn));
        }

        // $entity->setPostingDate($snapshot->postingDate);
        if ($snapshot->postingDate !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->postingDate));
        }

        // $entity->setGrDate($snapshot->grDate);
        if ($snapshot->grDate !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->grDate));
        }

        // $entity->setReversalDate($snapshot->reversalDate);
        if ($snapshot->reversalDate !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->reversalDate));
        }

        // REFERRENCE MAPPING
        // ++++++++++++++++++++++++++++++++
        $entity->setVendor($snapshot->vendor);
        if ($snapshot->vendor > 0) {
            /**
             *
             * @var \Application\Entity\NmtBpVendor $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($snapshot->vendor);

            $entity->setVendor($obj);
            $entity->setVendorName($obj->getVendorName());
        }

        $entity->setWarehouse($snapshot->warehouse);
        if ($snapshot->warehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->warehouse);
            $entity->setWarehouse($obj);
        }

        $entity->setCreatedBy($snapshot->createdBy);
        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        $entity->setLastchangeBy($snapshot->lastchangeBy);

        if ($snapshot->lastchangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastchangeBy);

            $entity->setLastchangeBy($obj);
        }

        $entity->setCurrency($snapshot->currency);
        if ($snapshot->currency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->currency);

            $entity->setCurrency($obj);
        }

        $entity->setLocalCurrency($snapshot->localCurrency);
        if ($snapshot->localCurrency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->localCurrency);

            $entity->setLocalCurrency($obj);
        }

        $entity->setDocCurrency($snapshot->docCurrency);
        if ($snapshot->docCurrency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->docCurrency);
            $entity->setDocCurrency($obj);
        }

        $entity->setPostingPeriod($snapshot->postingPeriod);
        if ($snapshot->postingPeriod > 0) {
            /**
             *
             * @var \Application\Entity\NmtFinPostingPeriod $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->find($snapshot->postingPeriod);
            $entity->setPostingPeriod($obj);
        }

        $entity->setCompany($snapshot->company);
        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param GRRowSnapshot $snapshot
     * @param NmtProcureGrRow $entity
     * @return NULL|\Application\Entity\NmtProcureGrRow
     */
    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, GRRowSnapshot $snapshot, NmtProcureGrRow $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setId($snapshot->id);
        $entity->setToken($snapshot->token);
        $entity->setRowNumber($snapshot->rowNumber);
        $entity->setRowIdentifer($snapshot->rowIdentifer);
        $entity->setQuantity($snapshot->quantity);
        $entity->setUnitPrice($snapshot->unitPrice);
        $entity->setNetAmount($snapshot->netAmount);
        $entity->setUnit($snapshot->unit);
        $entity->setItemUnit($snapshot->itemUnit);
        $entity->setConversionFactor($snapshot->conversionFactor);
        $entity->setConverstionText($snapshot->converstionText);
        $entity->setTaxRate($snapshot->taxRate);
        $entity->setRemarks($snapshot->remarks);
        $entity->setIsActive($snapshot->isActive);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setVendorItemCode($snapshot->vendorItemCode);
        $entity->setTraceStock($snapshot->traceStock);
        $entity->setGrossAmount($snapshot->grossAmount);
        $entity->setTaxAmount($snapshot->taxAmount);
        $entity->setFaRemarks($snapshot->faRemarks);
        $entity->setDiscountRate($snapshot->discountRate);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setTargetObject($snapshot->targetObject);
        $entity->setSourceObject($snapshot->sourceObject);
        $entity->setTargetObjectId($snapshot->targetObjectId);
        $entity->setSourceObjectId($snapshot->sourceObjectId);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionType($snapshot->transactionType);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setExwUnitPrice($snapshot->exwUnitPrice);
        $entity->setTotalExwPrice($snapshot->totalExwPrice);
        $entity->setConvertedPurchaseQuantity($snapshot->convertedPurchaseQuantity);
        $entity->setConvertedStandardQuantity($snapshot->convertedStandardQuantity);
        $entity->setConvertedStandardUnitPrice($snapshot->convertedStandardUnitPrice);
        $entity->setConvertedStockQuantity($snapshot->convertedStockQuantity);
        $entity->setConvertedStockUnitPrice($snapshot->convertedStockUnitPrice);
        $entity->setDocQuantity($snapshot->docQuantity);
        $entity->setDocUnitPrice($snapshot->docUnitPrice);
        $entity->setDocUnit($snapshot->docUnit);
        $entity->setConvertedPurchaseUnitPrice($snapshot->convertedPurchaseUnitPrice);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setReversalDoc($snapshot->reversalDoc);
        $entity->setFlow($snapshot->flow);
        $entity->setDocType($snapshot->docType);
        $entity->setVendorItemName($snapshot->vendorItemName);
        $entity->setDescriptionText($snapshot->descriptionText);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setUuid($snapshot->uuid);

        // DATE MAPPING
        // ++++++++++++++++++++++++++++++++

        // $entity->setCreatedOn($snapshot->createdOn);
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        // $entity->setLastchangeOn($snapshot->lastchangeOn);
        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        // $entity->setGrDate($snapshot->grDate);
        if ($snapshot->grDate !== null) {
            $entity->setGrDate(new \DateTime($snapshot->reversalDate));
        }

        // $entity->setReversalDate($snapshot->reversalDate);
        if ($snapshot->reversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->reversalDate));
        }

        // REFERRENCE MAPPING
        // ++++++++++++++++++++++++++++++++

        // $entity->setInvoice($snapshot->invoice);
        if ($snapshot->invoice > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoice $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->find($snapshot->invoice);
            $entity->setInvoice($obj);
        }

        // $entity->setGr($snapshot->gr);
        if ($snapshot->gr > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureGr $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureGr')->find($snapshot->gr);
            $entity->gr($obj);
        }

        // $entity->setApInvoiceRow($snapshot->apInvoiceRow);
        if ($snapshot->apInvoiceRow > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoiceRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->find($snapshot->apInvoiceRow);
            $entity->setApInvoiceRow($obj);
        }
        // $entity->setGlAccount($snapshot->glAccount);
        if ($snapshot->glAccount > 0) {
            /**
             *
             * @var \Application\Entity\FinAccount $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinAccount')->find($snapshot->glAccount);
            $entity->setGlAccount($obj);
        }
        // $entity->setCostCenter($snapshot->costCenter);
        if ($snapshot->costCenter > 0) {
            /**
             *
             * @var \Application\Entity\FinCostCenter $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($snapshot->costCenter);
            $entity->setCostCenter($obj);
        }

        // $entity->setDocUom($snapshot->docUom);
        if ($snapshot->docUom > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationUom $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->find($snapshot->docUom);
            $entity->setDocUom($obj);
        }

        // $entity->setPrRow($snapshot->prRow);
        if ($snapshot->prRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureGrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->find($snapshot->prRow);
            $entity->setPrRow($obj);
        }

        // $entity->setCreatedBy($snapshot->createdBy);
        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        // $entity->setWarehouse($snapshot->warehouse);
        if ($snapshot->warehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->warehouse);
            $entity->setWarehouse($obj);
        }

        // $entity->setLastchangedBy($snapshot->lastchangedBy);
        if ($snapshot->lastchangedBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastchangedBy);
            $entity->setLastchangedBy($obj);
        }

        // $entity->setItem($snapshot->item);
        if ($snapshot->item > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->item);
            $entity->setItem($obj);
        }

        // $entity->setPoRow($snapshot->poRow);
        if ($snapshot->poRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePoRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($snapshot->poRow);
            $entity->setPoRow($obj);
        }
    }

    /**
     * 
     * @param NmtProcureGr $entity
     * @return NULL|\Procure\Domain\GoodsReceipt\GRDetailsSnapshot
     */
    public static function createDetailSnapshot(NmtProcureGr $entity = null)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new GRDetailsSnapshot();

        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->invoiceNo = $entity->getInvoiceNo();
        $snapshot->invoiceDate = $entity->getInvoiceDate();
        $snapshot->currencyIso3 = $entity->getCurrencyIso3();
        $snapshot->exchangeRate = $entity->getExchangeRate();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->trxType = $entity->getTrxType();
        $snapshot->sapDoc = $entity->getSapDoc();
        $snapshot->contractNo = $entity->getContractNo();
        $snapshot->quotationNo = $entity->getQuotationNo();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->deliveryMode = $entity->getDeliveryMode();
        $snapshot->incoterm = $entity->getIncoterm();
        $snapshot->incotermPlace = $entity->getIncotermPlace();
        $snapshot->paymentTerm = $entity->getPaymentTerm();
        $snapshot->paymentMethod = $entity->getPaymentMethod();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->isReversed = $entity->getIsReversed();
        $snapshot->reversalDoc = $entity->getReversalDoc();
        $snapshot->reversalReason = $entity->getReversalReason();
        $snapshot->docType = $entity->getDocType();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->docVersion = $entity->getDocVersion();

        // Date Reference
        // ==========================================
        $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        $snapshot->lastchangeOn = $entity->getLastchangeOn();
        if (! $entity->getLastchangeOn() == null) {
            $snapshot->lastchangeOn = $entity->getLastchangeOn()->format("Y-m-d H:i:s");
        }

        $snapshot->postingDate = $entity->getPostingDate();
        if (! $entity->getPostingDate() == null) {
            $snapshot->postingDate = $entity->getPostingDate()->format("Y-m-d H:i:s");
        }

        $snapshot->grDate = $entity->getGrDate();
        if (! $entity->getGrDate() == null) {
            $snapshot->grDate = $entity->getGrDate()->format("Y-m-d H:i:s");
        }

        $snapshot->contractDate = $entity->getContractDate();
        if (! $entity->getContractDate() == null) {
            $snapshot->contractDate = $entity->getContractDate()->format("Y-m-d H:i:s");
        }

        $snapshot->quotationDate = $entity->getQuotationDate();
        if (! $entity->getQuotationDate() == null) {
            $snapshot->quotationDate = $entity->getQuotationDate()->format("Y-m-d H:i:s");
        }

        $snapshot->reversalDate = $entity->getReversalDate();
        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d H:i:s");
        }

        // Mapping Reference
        // ==========================================
        $snapshot->vendor = $entity->getVendor();
        if ($entity->getVendor() !== null) {
            $snapshot->vendor = $entity->getVendor()->getId();
        }

        $snapshot->warehouse = $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
        }

        $snapshot->createdBy = $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        $snapshot->lastchangeBy = $entity->getLastchangeBy();
        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
        }

        $snapshot->currency = $entity->getCurrency();
        if ($entity->getCurrency() !== null) {
            $snapshot->currency = $entity->getCurrency()->getId();
        }

        $snapshot->localCurrency = $entity->getLocalCurrency();
        if ($entity->getLocalCurrency() !== null) {
            $snapshot->localCurrency = $entity->getLocalCurrency()->getId();
        }

        $snapshot->docCurrency = $entity->getDocCurrency();
        if ($entity->getDocCurrency() !== null) {
            $snapshot->docCurrency = $entity->getDocCurrency()->getId();
        }

        $snapshot->postingPeriod = $entity->getPostingPeriod();
        if ($entity->getPostingPeriod() !== null) {
            $snapshot->postingPeriod = $entity->getPostingPeriod()->getId();
        }

        $snapshot->company = $entity->getCompany();
        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        return $snapshot;
    }

   /**
    * 
    * @param NmtProcureGrRow $entity
    * @return NULL|\Procure\Domain\GoodsReceipt\GRRowDetailsSnapshot
    */
    public static function createRowDetailSnapshot(NmtProcureGrRow $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new GRRowDetailsSnapshot();

        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->rowNumber = $entity->getRowNumber();
        $snapshot->rowIdentifer = $entity->getRowIdentifer();
        $snapshot->quantity = $entity->getQuantity();
        $snapshot->unitPrice = $entity->getUnitPrice();
        $snapshot->netAmount = $entity->getNetAmount();
        $snapshot->unit = $entity->getUnit();
        $snapshot->itemUnit = $entity->getItemUnit();
        $snapshot->conversionFactor = $entity->getConversionFactor();
        $snapshot->converstionText = $entity->getConverstionText();
        $snapshot->taxRate = $entity->getTaxRate();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->vendorItemCode = $entity->getVendorItemCode();
        $snapshot->traceStock = $entity->getTraceStock();
        $snapshot->grossAmount = $entity->getGrossAmount();
        $snapshot->taxAmount = $entity->getTaxAmount();
        $snapshot->faRemarks = $entity->getFaRemarks();
        $snapshot->discountRate = $entity->getDiscountRate();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->targetObject = $entity->getTargetObject();
        $snapshot->sourceObject = $entity->getSourceObject();
        $snapshot->targetObjectId = $entity->getTargetObjectId();
        $snapshot->sourceObjectId = $entity->getSourceObjectId();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionType = $entity->getTransactionType();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->exwUnitPrice = $entity->getExwUnitPrice();
        $snapshot->totalExwPrice = $entity->getTotalExwPrice();
        $snapshot->convertedPurchaseQuantity = $entity->getConvertedPurchaseQuantity();
        $snapshot->convertedStandardQuantity = $entity->getConvertedStandardQuantity();
        $snapshot->convertedStandardUnitPrice = $entity->getConvertedStandardUnitPrice();
        $snapshot->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $snapshot->convertedStockUnitPrice = $entity->getConvertedStockUnitPrice();
        $snapshot->docQuantity = $entity->getDocQuantity();
        $snapshot->docUnitPrice = $entity->getDocUnitPrice();
        $snapshot->docUnit = $entity->getDocUnit();
        $snapshot->convertedPurchaseUnitPrice = $entity->getConvertedPurchaseUnitPrice();
        $snapshot->isReversed = $entity->getIsReversed();
        $snapshot->reversalReason = $entity->getReversalReason();
        $snapshot->reversalDoc = $entity->getReversalDoc();
        $snapshot->flow = $entity->getFlow();
        $snapshot->docType = $entity->getDocType();
        $snapshot->vendorItemName = $entity->getVendorItemName();
        $snapshot->descriptionText = $entity->getDescriptionText();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->uuid = $entity->getUuid();

        // Date Reference
        // ==========================================
        // $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->lastchangeOn = $entity->getLastchangeOn();
        if (! $entity->getLastchangeOn() == null) {
            $snapshot->lastchangeOn = $entity->getLastchangeOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->grDate = $entity->getGrDate();
        if (! $entity->getGrDate() == null) {
            $snapshot->grDate = $entity->getGrDate()->format("Y-m-d H:i:s");
        }

        // $snapshot->reversalDate = $entity->getReversalDate();
        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d H:i:s");
        }

        // Mapping Reference
        // ==========================================

        // $snapshot->invoice = $entity->getInvoice();
        if ($entity->getInvoice() !== null) {
            $snapshot->invoice = $entity->getInvoice()->getId();
        }

        // $snapshot->gr = $entity->getGr();
        if ($entity->getGr() !== null) {
            $snapshot->gr = $entity->getGr()->getId();
        }

        // $snapshot->apInvoiceRow = $entity->getApInvoiceRow();
        if ($entity->getApInvoiceRow() !== null) {
            $snapshot->apInvoiceRow = $entity->getApInvoiceRow()->getId();
        }

        // $snapshot->glAccount = $entity->getGlAccount();
        if ($entity->getGlAccount() !== null) {
            $snapshot->glAccount = $entity->getGlAccount()->getId();
        }

        $snapshot->costCenter = $entity->getCostCenter();
        if ($entity->getCostCenter() !== null) {
            $snapshot->costCenter = $entity->getCostCenter()->getId();
        }

        $snapshot->docUom = $entity->getDocUom();
        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }

        $snapshot->prRow = $entity->getPrRow();
        if ($entity->getPrRow() !== null) {
            $snapshot->prRow = $entity->getPrRow()->getId();
        }

        $snapshot->createdBy = $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        $snapshot->warehouse = $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
        }

        $snapshot->lastchangedBy = $entity->getLastchangedBy();
        if ($entity->getLastchangedBy() !== null) {
            $snapshot->lastchangedBy = $entity->getLastchangedBy()->getId();
        }
        
        $snapshot->item = $entity->getItem();
        if ($entity->getItem() !== null) {
            $snapshot->item = $entity->getItem()->getId();
        }
        
        $snapshot->poRow= $entity->getPoRow();
        if ($entity->getPoRow() !== null) {
            $snapshot->poRow = $entity->getPoRow()->getId();
        }
        return $snapshot;
    }
}
