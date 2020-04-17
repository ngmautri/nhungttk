<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\NmtProcureGr;
use Application\Entity\NmtProcureGrRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\GoodsReceipt\GRDetailsSnapshot;
use Procure\Domain\GoodsReceipt\GRRowDetailsSnapshot;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Domain\GoodsReceipt\GRSnapshot;

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
     * @param GRSnapshot $snapshot
     * @param NmtProcureGr $entity
     * @return NULL|\Application\Entity\NmtProcureGr
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, GRSnapshot $snapshot, NmtProcureGr $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        // $entity->setId($snapshot->id);
        $entity->setToken($snapshot->token);
        $entity->setVendorName($snapshot->vendorName);
        $entity->setInvoiceNo($snapshot->invoiceNo);
        $entity->setCurrencyIso3($snapshot->currencyIso3);
        $entity->setExchangeRate($snapshot->exchangeRate);
        $entity->setRemarks($snapshot->remarks);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setIsActive($snapshot->isActive);
        $entity->setTrxType($snapshot->trxType);
        $entity->setSapDoc($snapshot->sapDoc);
        $entity->setContractNo($snapshot->contractNo);
        $entity->setQuotationNo($snapshot->quotationNo);
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
        $entity->setDocNumber($snapshot->docNumber);

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         * $entity->setPostingDate($snapshot->postingDate);
         * $entity->setGrDate($snapshot->grDate);
         * $entity->setContractDate($snapshot->contractDate);
         * $entity->setQuotationDate($snapshot->quotationDate);
         * $entity->setReversalDate($snapshot->reversalDate);
         * $entity->setInvoiceDate($snapshot->invoiceDate);
         * $entity->setDocDate($snapshot->docDate);
         */

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        if ($snapshot->postingDate !== null) {
            $entity->setPostingDate(new \DateTime($snapshot->postingDate));
        }

        if ($snapshot->grDate !== null) {
            $entity->setGrDate(new \DateTime($snapshot->grDate));
        }

        if ($snapshot->contractDate !== null) {
            $entity->setContractDate(new \DateTime($snapshot->contractDate));
        }
        if ($snapshot->quotationDate !== null) {
            $entity->setQuotationDate(new \DateTime($snapshot->quotationDate));
        }
        if ($snapshot->reversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->reversalDate));
        }

        if ($snapshot->invoiceDate !== null) {
            $entity->setInvoiceDate(new \DateTime($snapshot->invoiceDate));
        }

        if ($snapshot->docDate !== null) {
            $entity->setDocDate(new \DateTime($snapshot->docDate));
        }
        // ==========

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setVendor($snapshot->vendor);
         * $entity->setWarehouse($snapshot->warehouse);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setCurrency($snapshot->currency);
         * $entity->setLocalCurrency($snapshot->localCurrency);
         * $entity->setDocCurrency($snapshot->docCurrency);
         * $entity->setPostingPeriod($snapshot->postingPeriod);
         * $entity->setCompany($snapshot->company);
         */

        if ($snapshot->vendor > 0) {
            /**
             *
             * @var \Application\Entity\NmtBpVendor $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($snapshot->vendor);

            $entity->setVendor($obj);
            $entity->setVendorName($obj->getVendorName());
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

        // $entity->setCreatedBy($snapshot->createdBy);
        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        // $entity->setLastchangeBy($snapshot->lastchangeBy);
        if ($snapshot->lastchangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastchangeBy);

            $entity->setLastchangeBy($obj);
        }

        // $entity->setCurrency($snapshot->currency);
        if ($snapshot->currency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->currency);

            $entity->setCurrency($obj);
        }

        // $entity->setLocalCurrency($snapshot->localCurrency);
        if ($snapshot->localCurrency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->localCurrency);

            $entity->setLocalCurrency($obj);
        }

        // $entity->setDocCurrency($snapshot->docCurrency);
        if ($snapshot->docCurrency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->docCurrency);
            $entity->setDocCurrency($obj);
        }

        // $entity->setPostingPeriod($snapshot->postingPeriod);
        if ($snapshot->postingPeriod > 0) {
            /**
             *
             * @var \Application\Entity\NmtFinPostingPeriod $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->find($snapshot->postingPeriod);
            $entity->setPostingPeriod($obj);
        }

        // $entity->setCompany($snapshot->company);
        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        // ===================
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

        // =================================
        // Mapping None-Object Field
        // =================================

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
        $entity->setConvertFactorPurchase($snapshot->convertFactorPurchase);
        $entity->setDocVersion($snapshot->docVersion);
        $entity->setStandardConvertFactor($snapshot->standardConvertFactor);

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         * $entity->setGrDate($snapshot->grDate);
         * $entity->setReversalDate($snapshot->reversalDate);
         */
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        if ($snapshot->grDate !== null) {
            $entity->setGrDate(new \DateTime($snapshot->reversalDate));
        }

        if ($snapshot->reversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->reversalDate));
        }

        // =================

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setInvoice($snapshot->invoice);
         * $entity->setGr($snapshot->gr);
         * $entity->setApInvoiceRow($snapshot->apInvoiceRow);
         * $entity->setGlAccount($snapshot->glAccount);
         * $entity->setCostCenter($snapshot->costCenter);
         * $entity->setDocUom($snapshot->docUom);
         * $entity->setPrRow($snapshot->prRow);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setWarehouse($snapshot->warehouse);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setItem($snapshot->item);
         * $entity->setPoRow($snapshot->poRow);
         */

        // =================

        if ($snapshot->invoice > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoice $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->find($snapshot->invoice);
            $entity->setInvoice($obj);
        }

        if ($snapshot->gr > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureGr $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureGr')->find($snapshot->gr);
            $entity->setGr($obj);
        }

        if ($snapshot->apInvoiceRow > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoiceRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->find($snapshot->apInvoiceRow);
            $entity->setApInvoiceRow($obj);
        }

        if ($snapshot->glAccount > 0) {
            /**
             *
             * @var \Application\Entity\FinAccount $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinAccount')->find($snapshot->glAccount);
            $entity->setGlAccount($obj);
        }

        if ($snapshot->costCenter > 0) {
            /**
             *
             * @var \Application\Entity\FinCostCenter $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($snapshot->costCenter);
            $entity->setCostCenter($obj);
        }

        if ($snapshot->docUom > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationUom $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->find($snapshot->docUom);
            $entity->setDocUom($obj);
        }

        if ($snapshot->prRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($snapshot->prRow);
            $entity->setPrRow($obj);
        }

        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        if ($snapshot->warehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->warehouse);
            $entity->setWarehouse($obj);
        }

        if ($snapshot->lastchangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastchangeBy);
            $entity->setLastchangeBy($obj);
        }

        if ($snapshot->item > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->item);
            $entity->setItem($obj);
        }

        if ($snapshot->poRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePoRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($snapshot->poRow);
            $entity->setPoRow($obj);
        }

        // ++++++++++++++++++++++++++++++++
        return $entity;
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

        // =================================
        // Mapping None-Object Field
        // =================================
        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->invoiceNo = $entity->getInvoiceNo();
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
        $snapshot->docNumber = $entity->getDocNumber();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->invoiceDate = $entity->getInvoiceDate();
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->postingDate = $entity->getPostingDate();
         * $snapshot->grDate = $entity->getGrDate();
         * $snapshot->contractDate = $entity->getContractDate();
         * $snapshot->quotationDate = $entity->getQuotationDate();
         * $snapshot->reversalDate = $entity->getReversalDate();
         * $snapshot->docDate = $entity->getDocDate();
         */

        if (! $entity->getInvoiceDate() == null) {
            $snapshot->invoiceDate = $entity->getInvoiceDate()->format("Y-m-d");
        }

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getPostingDate() == null) {
            $snapshot->postingDate = $entity->getPostingDate()->format("Y-m-d");
        }

        if (! $entity->getGrDate() == null) {
            $snapshot->grDate = $entity->getGrDate()->format("Y-m-d");
        }
        if (! $entity->getContractDate() == null) {
            $snapshot->contractDate = $entity->getContractDate()->format("Y-m-d");
        }

        // $snapshot->quotationDate = $entity->getQuotationDate();
        if (! $entity->getQuotationDate() == null) {
            $snapshot->quotationDate = $entity->getQuotationDate()->format("Y-m-d");
        }

        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
        }
        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d");
        }

        // -----------------------

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $snapshot->vendor = $entity->getVendor();
         * $snapshot->warehouse = $entity->getWarehouse();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->currency = $entity->getCurrency();
         * $snapshot->localCurrency = $entity->getLocalCurrency();
         * $snapshot->docCurrency = $entity->getDocCurrency();
         * $snapshot->postingPeriod = $entity->getPostingPeriod();
         * $snapshot->company = $entity->getCompany();
         */

        if ($entity->getVendor() !== null) {
            HeaderMapper::updateVendorDetails($snapshot, $entity->getVendor());
        }

        if ($entity->getWarehouse() !== null) {
            HeaderMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = $entity->getCreatedBy()->getFirstname() . " " . $entity->getCreatedBy()->getLastname();
        }

        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangedByName = $entity->getLastchangeBy()->getFirstname() . " " . $entity->getLastchangeBy()->getLastname();
        }

        if ($entity->getCurrency() !== null) {
            $snapshot->currency = $entity->getCurrency()->getId();
        }

        if ($entity->getLocalCurrency() !== null) {
            HeaderMapper::updateLocalCurrencyDetails($snapshot, $entity->getLocalCurrency());
        }

        if ($entity->getDocCurrency() !== null) {
            HeaderMapper::updateDocCurrencyDetails($snapshot, $entity->getDocCurrency());
        }

        if ($entity->getPostingPeriod() !== null) {
            HeaderMapper::updatePostingPeriodDetails($snapshot, $entity->getPostingPeriod());
        }

        if ($entity->getCompany() !== null) {
            HeaderMapper::updateCompanyDetails($snapshot, $entity->getCompany());
        }

        // -------------------------

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

        // =================================
        // Mapping None-Object Field
        // =================================

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
        $snapshot->convertFactorPurchase = $entity->getConvertFactorPurchase();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->standardConvertFactor = $entity->getStandardConvertFactor();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->grDate = $entity->getGrDate();
         * $snapshot->reversalDate = $entity->getReversalDate();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->lastchangeOn = $entity->getLastchangeOn();
        if (! $entity->getLastchangeOn() == null) {
            $snapshot->lastchangeOn = $entity->getLastchangeOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->grDate = $entity->getGrDate();
        if (! $entity->getGrDate() == null) {
            $snapshot->grDate = $entity->getGrDate()->format("Y-m-d");
        }

        // $snapshot->reversalDate = $entity->getReversalDate();
        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
        }

        // ------------------------

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $snapshot->invoice = $entity->getInvoice();
         * $snapshot->gr = $entity->getGr();
         * $snapshot->apInvoiceRow = $entity->getApInvoiceRow();
         * $snapshot->glAccount = $entity->getGlAccount();
         * $snapshot->costCenter = $entity->getCostCenter();
         * $snapshot->docUom = $entity->getDocUom();
         * $snapshot->prRow = $entity->getPrRow();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->warehouse = $entity->getWarehouse();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->item = $entity->getItem();
         * $snapshot->poRow = $entity->getPoRow();
         */

        if ($entity->getInvoice() !== null) {
            $snapshot->invoice = $entity->getInvoice()->getId();
        }

        if ($entity->getGr() !== null) {
            $snapshot->gr = $entity->getGr()->getId();
            RowMapper::updateGRDetails($snapshot, $entity->getGr()); // update Parent Detail.
        }

        if ($entity->getApInvoiceRow() !== null) {
            $snapshot->apInvoiceRow = $entity->getApInvoiceRow()->getId();
        }

        if ($entity->getGlAccount() !== null) {
            RowMapper::updateGLAccountDetails($snapshot, $entity->getGlAccount());
        }

        if ($entity->getCostCenter() !== null) {
            RowMapper::updateCostCenterDetails($snapshot, $entity->getCostCenter());
        }

        if ($entity->getDocUom() !== null) {
            RowMapper::updateUomDetails($snapshot, $entity->getDocUom());
        }

        if ($entity->getPrRow() !== null) {
            RowMapper::updatePRDetails($snapshot, $entity->getPrRow());
        }

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = sprintf("%s %s", $entity->getCreatedBy()->getFirstname(), $entity->getCreatedBy()->getFirstname());
        }

        if ($entity->getWarehouse() !== null) {
            RowMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        if ($entity->getLastchangeBy()) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangeByName = sprintf("%s %s", $entity->getLastchangeBy()->getFirstname(), $entity->getLastchangeBy()->getFirstname());
        }

        if ($entity->getItem() !== null) {
            RowMapper::updateItemDetails($snapshot, $entity->getItem());
        }

        if ($entity->getPoRow() !== null) {
            $snapshot->poRow = $entity->getPoRow()->getId();

            if ($entity->getPoRow()->getPo() !== null) {
                $snapshot->po = $entity->getPoRow()
                    ->getPo()
                    ->getId();
            }
        }

        // -----------------------------

        return $snapshot;
    }
}
