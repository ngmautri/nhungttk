<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\NmtProcurePo;
use Application\Entity\NmtProcurePoRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoMapper
{

    /**
     *
     * @param POSnapshot $snapshot
     * @param NmtProcurePo $entity
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, POSnapshot $snapshot, NmtProcurePo $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

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
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setDocType($snapshot->docType);
        $entity->setPaymentStatus($snapshot->paymentStatus);
        $entity->setTotalDocValue($snapshot->totalDocValue);
        $entity->setTotalDocTax($snapshot->totalDocTax);
        $entity->setTotalDocDiscount($snapshot->totalDocDiscount);
        $entity->setTotalLocalValue($snapshot->totalLocalValue);
        $entity->setTotalLocalTax($snapshot->totalLocalTax);
        $entity->setTotalLocalDiscount($snapshot->totalLocalDiscount);
        $entity->setReversalBlocked($snapshot->reversalBlocked);

        $entity->setUuid($snapshot->uuid);
        $entity->setDocVersion($snapshot->docVersion);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setReversalDate($snapshot->reversalDate);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setPostingPeriod($snapshot->postingPeriod);
        $entity->setCurrentStatus($snapshot->currentStatus);
        $entity->setTransactionType($snapshot->transactionType);
        $entity->setDiscountRate($snapshot->discountRate);
        $entity->setDiscountAmount($snapshot->discountAmount);
        $entity->setDocNumber($snapshot->docNumber);
        $entity->setBaseDocId($snapshot->baseDocId);
        $entity->setBaseDocType($snapshot->baseDocType);
        $entity->setTargetDocId($snapshot->targetDocId);
        $entity->setTargetDocType($snapshot->targetDocType);
        $entity->setClearingDocId($snapshot->clearingDocId);

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $entity->setInvoiceDate($snapshot->invoiceDate);
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         * $entity->setPostingDate($snapshot->postingDate);
         * $entity->setGrDate($snapshot->grDate);
         * $entity->setContractDate($snapshot->contractDate);
         * $entity->setQuotationDate($snapshot->quotationDate);
         * $entity->setReversalDate($snapshot->reversalDate);
         * $entity->setDocDate($snapshot->docDate);
         */

        // =================
        if ($snapshot->invoiceDate !== null) {
            $entity->setInvoiceDate(new \DateTime($snapshot->invoiceDate));
        }

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

        // $entity->setQuotationDate($snapshot->quotationDate);
        if ($snapshot->quotationDate !== null) {
            $entity->setQuotationDate(new \DateTime($snapshot->quotationDate));
        }

        if ($snapshot->reversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->reversalDate));
        }

        if ($snapshot->docDate !== null) {
            $entity->setDocDate(new \DateTime($snapshot->docDate));
        }

        // Overwrite.
        $entity->setContractNo($snapshot->getDocNumber());
        $entity->setContractDate($entity->getDocDate());

        // =================

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setVendor($snapshot->vendor);
         * $entity->setPmtTerm($snapshot->pmtTerm);
         * $entity->setCompany($snapshot->company);
         * $entity->setWarehouse($snapshot->warehouse);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setCurrency($snapshot->currency);
         * $entity->setPaymentMethod($snapshot->paymentMethod);
         * $entity->setLocalCurrency($snapshot->localCurrency);
         * $entity->setDocCurrency($snapshot->docCurrency);
         * $entity->setIncoterm2($snapshot->incoterm2);
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

        if ($snapshot->pmtTerm > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationPmtTerm $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->find($snapshot->pmtTerm);
            $entity->setPmtTerm($obj);
        }

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        if ($snapshot->warehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->warehouse);
            $entity->setWarehouse($obj);
        }

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

        if ($snapshot->currency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->currency);
            $entity->setCurrency($obj);
        }

        if ($snapshot->paymentMethod > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationPmtMethod $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationPmtMethod')->find($snapshot->paymentMethod);
            $entity->setPaymentMethod($obj);
        }

        if ($snapshot->localCurrency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->localCurrency);
            $entity->setLocalCurrency($obj);
        }

        if ($snapshot->docCurrency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->docCurrency);
            $entity->setDocCurrency($obj);
            $entity->setCurrencyIso3($obj->getCurrency());
        }

        if ($snapshot->incoterm2 > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationIncoterms $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationIncoterms')->find($snapshot->incoterm2);
            $entity->setIncoterm2($obj);
        }

        // ==============
        return $entity;
    }

    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, PORowSnapshot $snapshot, NmtProcurePoRow $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setId($snapshot->id);
        $entity->setRowNumber($snapshot->rowNumber);
        $entity->setToken($snapshot->token);
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
        $entity->setRowIdentifer($snapshot->rowIdentifer);
        $entity->setDiscountRate($snapshot->discountRate);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setTargetObject($snapshot->targetObject);
        $entity->setSourceObject($snapshot->sourceObject);
        $entity->setTargetObjectId($snapshot->targetObjectId);
        $entity->setSourceObjectId($snapshot->sourceObjectId);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setExwUnitPrice($snapshot->exwUnitPrice);
        $entity->setTotalExwPrice($snapshot->totalExwPrice);
        $entity->setConvertFactorPurchase($snapshot->convertFactorPurchase);
        $entity->setConvertedPurchaseQuantity($snapshot->convertedPurchaseQuantity);
        $entity->setConvertedStandardQuantity($snapshot->convertedStandardQuantity);
        $entity->setConvertedStockQuantity($snapshot->convertedStockQuantity);
        $entity->setConvertedStandardUnitPrice($snapshot->convertedStandardUnitPrice);
        $entity->setConvertedStockUnitPrice($snapshot->convertedStockUnitPrice);
        $entity->setDocQuantity($snapshot->docQuantity);
        $entity->setDocUnit($snapshot->docUnit);
        $entity->setDocUnitPrice($snapshot->docUnitPrice);
        $entity->setConvertedPurchaseUnitPrice($snapshot->convertedPurchaseUnitPrice);
        $entity->setDocType($snapshot->docType);
        $entity->setDescriptionText($snapshot->descriptionText);
        $entity->setVendorItemName($snapshot->vendorItemName);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setDocVersion($snapshot->docVersion);

        $entity->setUuid($snapshot->uuid);
        $entity->setLocalUnitPrice($snapshot->localUnitPrice);
        $entity->setExwCurrency($snapshot->exwCurrency);
        $entity->setLocalNetAmount($snapshot->localNetAmount);
        $entity->setLocalGrossAmount($snapshot->localGrossAmount);
        $entity->setTransactionType($snapshot->transactionType);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setGlAccount($snapshot->glAccount);
        $entity->setCostCenter($snapshot->costCenter);
        $entity->setStandardConvertFactor($snapshot->standardConvertFactor);
        $entity->setClearingDocId($snapshot->clearingDocId);
        $entity->setBrand($snapshot->brand);

        // ============================
        // DATE MAPPING
        // ============================

        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         * $entity->setReversalDate($snapshot->reversalDate);
         */

        // $entity->setLastchangeOn($snapshot->lastchangeOn);
        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        // $entity->setCreatedOn($snapshot->createdOn);
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        // $entity->setReversalDate($snapshot->reversalDate);
        if ($snapshot->reversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->reversalDate));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $entity->setInvoice($snapshot->invoice);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setPrRow($snapshot->prRow);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setWarehouse($snapshot->warehouse);
         * $entity->setPo($snapshot->po);
         * $entity->setItem($snapshot->item);
         * $entity->setDocUom($snapshot->docUom);
         */

        // $entity->setInvoice($snapshot->invoice);
        if ($snapshot->invoice > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoice $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->find($snapshot->invoice);
            $entity->setInvoice($obj);
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

        // $entity->setPrRow($snapshot->prRow);
        if ($snapshot->prRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($snapshot->prRow);
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

        // entity->setPo($snapshot->po);
        if ($snapshot->po > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePo $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePo')->find($snapshot->po);
            $entity->setPo($obj);
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

        // $entity->setDocUom($snapshot->docUom);
        if ($snapshot->docUom > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationUom $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->find($snapshot->docUom);
            $entity->setDocUom($obj);
        }

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param NmtProcurePo $entity
     * @param object $snapshot
     * @return NULL|string|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public static function createSnapshot(EntityManager $doctrineEM, NmtProcurePo $entity = null, $snapshot = null)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new POSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->invoiceNo = $entity->getInvoiceNo();
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
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->docType = $entity->getDocType();
        $snapshot->paymentStatus = $entity->getPaymentStatus();
        $snapshot->totalDocValue = $entity->getTotalDocValue();
        $snapshot->totalDocTax = $entity->getTotalDocTax();
        $snapshot->totalDocDiscount = $entity->getTotalDocDiscount();
        $snapshot->totalLocalValue = $entity->getTotalLocalValue();
        $snapshot->totalLocalTax = $entity->getTotalLocalTax();
        $snapshot->totalLocalDiscount = $entity->getTotalLocalDiscount();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();

        $snapshot->uuid = $entity->getUuid();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->isReversed = $entity->getIsReversed();
        $snapshot->reversalDate = $entity->getReversalDate();
        $snapshot->reversalReason = $entity->getReversalReason();
        $snapshot->postingPeriod = $entity->getPostingPeriod();
        $snapshot->currentStatus = $entity->getCurrentStatus();
        $snapshot->transactionType = $entity->getTransactionType();
        $snapshot->discountRate = $entity->getDiscountRate();
        $snapshot->discountAmount = $entity->getDiscountAmount();
        $snapshot->docNumber = $entity->getDocNumber();
        $snapshot->baseDocId = $entity->getBaseDocId();
        $snapshot->baseDocType = $entity->getBaseDocType();
        $snapshot->targetDocId = $entity->getTargetDocId();
        $snapshot->targetDocType = $entity->getTargetDocType();
        $snapshot->clearingDocId = $entity->getClearingDocId();

        // ============================
        // REFERRENCE MAPPING
        // ============================

        if ($entity->getCompany() !== null) {
            HeaderMapper::updateCompanyDetails($snapshot, $entity->getCompany());
        }

        // $snapshot->vendor= $entity->getVendor();
        if ($entity->getVendor() !== null) {
            HeaderMapper::updateVendorDetails($snapshot, $entity->getVendor());
        }

        // $snapshot->pmtTerm = $entity->getPmtTerm();
        if ($entity->getPmtTerm() !== null) {
            HeaderMapper::updatePmtTermDetails($snapshot, $entity->getPmtTerm());
        }

        // $snapshot->warehouse = $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            HeaderMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        // $snapshot->createdBy = $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = $entity->getCreatedBy()->getFirstname() . " " . $entity->getCreatedBy()->getLastname();
        }

        // $snapshot->lastchangeBy = $entity->getLastchangeBy();
        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangedByName = $entity->getLastchangeBy()->getFirstname() . " " . $entity->getLastchangeBy()->getLastname();
        }

        // $snapshot->currency = $entity->getCurrency();
        if ($entity->getCurrency() !== null) {
            $snapshot->currency = $entity->getCurrency()->getId();
        }

        // $snapshot->paymentMethod = $entity->getPaymentMethod();
        if ($entity->getPaymentMethod() !== null) {
            HeaderMapper::updatePmtMethodDetails($snapshot, $entity->getPaymentMethod());
        }

        // $snapshot->localCurrency = $entity->getLocalCurrency();
        if ($entity->getLocalCurrency() !== null) {
            HeaderMapper::updateLocalCurrencyDetails($snapshot, $entity->getLocalCurrency());
        }

        // $snapshot->docCurrency = $entity->getDocCurrency();
        if ($entity->getDocCurrency() !== null) {
            HeaderMapper::updateDocCurrencyDetails($snapshot, $entity->getDocCurrency());
        }

        // $snapshot->incoterm2 = $entity->getIncoterm2();
        if ($entity->getIncoterm2() !== null) {
            $snapshot->incoterm2 = $entity->getIncoterm2()->getId();
            $snapshot->incotermCode = $entity->getIncoterm2()->getIncoterm();
            $snapshot->incotermName = $entity->getIncoterm2()->getIncotermDescription();
        }

        // ============================
        // DATE MAPPING
        // ============================

        // $snapshot->invoiceDate = $entity->getInvoiceDate();
        if (! $entity->getInvoiceDate() == null) {
            $snapshot->invoiceDate = $entity->getInvoiceDate()->format("Y-m-d");
        }

        $snapshot->postingDate = $entity->getPostingDate();
        if (! $entity->getPostingDate() == null) {
            $snapshot->postingDate = $entity->getPostingDate()->format("Y-m-d");
        }

        $snapshot->grDate = $entity->getGrDate();
        if (! $entity->getGrDate() == null) {
            $snapshot->grDate = $entity->getGrDate()->format("Y-m-d");
        }

        $snapshot->quotationDate = $entity->getQuotationDate();
        if (! $entity->getQuotationDate() == null) {
            $snapshot->quotationDate = $entity->getQuotationDate()->format("Y-m-d");
        }

        // $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->lastchangeOn = $entity->getLastchangeOn();
        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->contractDate= $entity->getContractDate();
        if (! $entity->getContractDate() == null) {
            $snapshot->contractDate = $entity->getContractDate()->format("Y-m-d");
        }

        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d");
        }

        return $snapshot;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param NmtProcurePoRow $entity
     * @param object $snapshot
     * @return NULL|string|\Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    public static function createRowSnapshot(EntityManager $doctrineEM, NmtProcurePoRow $entity, $snapshot = null)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new PORowSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================
        $snapshot->id = $entity->getId();
        $snapshot->rowNumber = $entity->getRowNumber();
        $snapshot->token = $entity->getToken();
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
        $snapshot->rowIdentifer = $entity->getRowIdentifer();
        $snapshot->discountRate = $entity->getDiscountRate();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->targetObject = $entity->getTargetObject();
        $snapshot->sourceObject = $entity->getSourceObject();
        $snapshot->targetObjectId = $entity->getTargetObjectId();
        $snapshot->sourceObjectId = $entity->getSourceObjectId();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->exwUnitPrice = $entity->getExwUnitPrice();
        $snapshot->totalExwPrice = $entity->getTotalExwPrice();
        $snapshot->convertFactorPurchase = $entity->getConvertFactorPurchase();
        $snapshot->convertedPurchaseQuantity = $entity->getConvertedPurchaseQuantity();
        $snapshot->convertedStandardQuantity = $entity->getConvertedStandardQuantity();
        $snapshot->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $snapshot->convertedStandardUnitPrice = $entity->getConvertedStandardUnitPrice();
        $snapshot->convertedStockUnitPrice = $entity->getConvertedStockUnitPrice();
        $snapshot->docQuantity = $entity->getDocQuantity();
        $snapshot->docUnit = $entity->getDocUnit();
        $snapshot->docUnitPrice = $entity->getDocUnitPrice();
        $snapshot->convertedPurchaseUnitPrice = $entity->getConvertedPurchaseUnitPrice();
        $snapshot->docType = $entity->getDocType();
        $snapshot->descriptionText = $entity->getDescriptionText();
        $snapshot->vendorItemName = $entity->getVendorItemName();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->localUnitPrice = $entity->getLocalUnitPrice();
        $snapshot->exwCurrency = $entity->getExwCurrency();
        $snapshot->localNetAmount = $entity->getLocalNetAmount();
        $snapshot->localGrossAmount = $entity->getLocalGrossAmount();
        $snapshot->transactionType = $entity->getTransactionType();
        $snapshot->isReversed = $entity->getIsReversed();
        $snapshot->standardConvertFactor = $entity->getStandardConvertFactor();

        $snapshot->glAccount = $entity->getGlAccount();
        $snapshot->costCenter = $entity->getCostCenter();

        $snapshot->clearingDocId = $entity->getClearingDocId();
        $snapshot->brand = $entity->getBrand();

        if ($entity->getGlAccount() > 0) {
            /**
             *
             * @var \Application\Entity\FinAccount $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinAccount')->find($entity->getGlAccount());
            RowMapper::updateGLAccountDetails($snapshot, $obj);
        }

        if ($entity->getCostCenter() > 0) {
            /**
             *
             * @var \Application\Entity\FinCostCenter $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinCostCenter')->find($entity->getCostCenter());
            RowMapper::updateCostCenterDetails($snapshot, $obj);
        }

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->reversalDate = $entity->getReversalDate();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $snapshot->invoice = $entity->getInvoice();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->prRow = $entity->getPrRow();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->warehouse = $entity->getWarehouse();
         * $snapshot->po = $entity->getPo();
         * $snapshot->item = $entity->getItem();
         * $snapshot->docUom = $entity->getDocUom();
         */

        // $snapshot->invoice= $entity->getInvoice();
        if ($entity->getInvoice() !== null) {
            $snapshot->invoice = $entity->getInvoice()->getId();
        }

        // $snapshot->lastchangeBy= $entity->getLastchangeBy();
        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangeByName = sprintf("%s %s", $entity->getLastchangeBy()->getFirstname(), $entity->getLastchangeBy()->getFirstname());
        }

        // $snapshot->prRow= $entity->getPrRow();
        if ($entity->getPrRow() !== null) {
            RowMapper::updateOnlyPRDetails($snapshot, $entity->getPrRow());
        }

        // $snapshot->createdBy= $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = sprintf("%s %s", $entity->getCreatedBy()->getFirstname(), $entity->getCreatedBy()->getFirstname());
        }

        // $snapshot->warehouse= $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            RowMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        // $snapshot->po= $entity->getPo();
        if ($entity->getPo() !== null) {
            RowMapper::updatePODetails($snapshot, $entity->getPo()); // Parent ID
        }

        // $snapshot->item= $entity->getItem();
        if ($entity->getItem() !== null) {
            RowMapper::updateItemDetails($snapshot, $entity->getItem());
        }

        // $snapshot->docUom = $entity->getDocUom();
        if ($entity->getDocUom() !== null) {
            RowMapper::updateUomDetails($snapshot, $entity->getDocUom());
        }
        return $snapshot;
    }
}
