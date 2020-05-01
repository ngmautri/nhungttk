<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Domain\AccountPayable\APSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApMapper
{

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     * @param \Procure\Domain\AccountPayable\APSnapshot $snapshot
     * @param \Application\Entity\FinVendorInvoice $entity
     * @return NULL|\Application\Entity\FinVendorInvoice
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, APSnapshot $snapshot, FinVendorInvoice $entity)
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
        $entity->setCurrentStatus($snapshot->currentStatus);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setPaymentTerm($snapshot->paymentTerm);
        $entity->setTransactionType($snapshot->transactionType);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setIncoterm($snapshot->incoterm);
        $entity->setIncotermPlace($snapshot->incotermPlace);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setReversalDoc($snapshot->reversalDoc);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setPaymentStatus($snapshot->paymentStatus);
        $entity->setIsReversable($snapshot->isReversable);
        $entity->setDocType($snapshot->docType);
        $entity->setTotalDocValue($snapshot->totalDocValue);
        $entity->setTotalDocTax($snapshot->totalDocTax);
        $entity->setTotalDocDiscount($snapshot->totalDocDiscount);
        $entity->setTotalLocalValue($snapshot->totalLocalValue);
        $entity->setTotalLocalTax($snapshot->totalLocalTax);
        $entity->setTotalLocalDiscount($snapshot->totalLocalDiscount);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setUuid($snapshot->uuid);
        $entity->setDiscountRate($snapshot->discountRate);
        $entity->setDiscountAmount($snapshot->discountAmount);
        $entity->setDocNumber($snapshot->getDocNumber());

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
         */

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

        if ($snapshot->quotationDate !== null) {
            $entity->setQuotationDate(new \DateTime($snapshot->quotationDate));
        }

        if ($snapshot->reversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->reversalDate));
        }

        if ($snapshot->docDate !== null) {
            $entity->setDocDate(new \DateTime($snapshot->docDate));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setVendor($snapshot->vendor);
         * $entity->setProcureGr($snapshot->procureGr);
         * $entity->setLocalCurrency($snapshot->localCurrency);
         * $entity->setDocCurrency($snapshot->docCurrency);
         * $entity->setPostingPeriod($snapshot->postingPeriod);
         * $entity->setIncoterm2($snapshot->incoterm2);
         * $entity->setPmtTerm($snapshot->pmtTerm);
         * $entity->setWarehouse($snapshot->warehouse);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setCurrency($snapshot->currency);
         * $entity->setPo($snapshot->po);
         * $entity->setCompany($snapshot->company);
         * $entity->setPaymentMethod($snapshot->paymentMethod);
         * $entity->setInventoryGr($snapshot->inventoryGr);
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

        if ($snapshot->procureGr > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureGr $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureGr')->find($snapshot->procureGr);

            $entity->setProcureGr($obj);
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

        if ($snapshot->postingPeriod > 0) {
            /**
             *
             * @var \Application\Entity\NmtFinPostingPeriod $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->find($snapshot->postingPeriod);
            $entity->setPostingPeriod($obj);
        }

        if ($snapshot->incoterm2 > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationIncoterms $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationIncoterms')->find($snapshot->incoterm2);
            $entity->setIncoterm2($obj);
        }

        if ($snapshot->pmtTerm > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationPmtTerm $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->find($snapshot->pmtTerm);
            $entity->setPmtTerm($obj);
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

        if ($snapshot->po > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePo $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePo')->find($snapshot->po);
            $entity->setPo($obj);
        }

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        if ($snapshot->paymentMethod > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationPmtMethod $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationPmtMethod')->find($snapshot->paymentMethod);
            $entity->setPaymentMethod($obj);
        }

        if ($snapshot->inventoryGr > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryGr $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryGr')->find($snapshot->inventoryGr);
            $entity->setInventoryGr($obj);
        }
        // =========

        return $entity;
    }

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     * @param \Procure\Domain\AccountPayable\APRowSnapshot $snapshot
     * @param \Application\Entity\FinVendorInvoiceRow $entity
     * @return NULL|\Application\Entity\FinVendorInvoiceRow
     */
    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, APRowSnapshot $snapshot, FinVendorInvoiceRow $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

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
        $entity->setLocalUnitPrice($snapshot->localUnitPrice);
        $entity->setDocUnitPrice($snapshot->docUnitPrice);
        $entity->setExwUnitPrice($snapshot->exwUnitPrice);
        $entity->setExwCurrency($snapshot->exwCurrency);
        $entity->setLocalNetAmount($snapshot->localNetAmount);
        $entity->setLocalGrossAmount($snapshot->localGrossAmount);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionType($snapshot->transactionType);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setTotalExwPrice($snapshot->totalExwPrice);
        $entity->setConvertFactorPurchase($snapshot->convertFactorPurchase);
        $entity->setConvertedPurchaseQuantity($snapshot->convertedPurchaseQuantity);
        $entity->setConvertedStockQuantity($snapshot->convertedStockQuantity);
        $entity->setConvertedStockUnitPrice($snapshot->convertedStockUnitPrice);
        $entity->setConvertedStandardQuantity($snapshot->convertedStandardQuantity);
        $entity->setConvertedStandardUnitPrice($snapshot->convertedStandardUnitPrice);
        $entity->setDocQuantity($snapshot->docQuantity);
        $entity->setDocUnit($snapshot->docUnit);
        $entity->setConvertedPurchaseUnitPrice($snapshot->convertedPurchaseUnitPrice);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setReversalDoc($snapshot->reversalDoc);
        $entity->setIsReversable($snapshot->isReversable);
        $entity->setDocType($snapshot->docType);
        $entity->setDescriptionText($snapshot->descriptionText);
        $entity->setVendorItemName($snapshot->vendorItemName);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setTargetObject($snapshot->targetObject);
        $entity->setTargetObjectId($snapshot->targetObjectId);
        $entity->setSourceObject($snapshot->sourceObject);
        $entity->setSourceObjectId($snapshot->sourceObjectId);
        $entity->setUuid($snapshot->uuid);
        $entity->setDocVersion($snapshot->docVersion);
        $entity->setStandardConvertFactor($snapshot->standardConvertFactor);

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         * $entity->setReversalDate($snapshot->reversalDate);
         */

        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->reversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->reversalDate));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setInvoice($snapshot->invoice);
         * $entity->setGlAccount($snapshot->glAccount);
         * $entity->setCostCenter($snapshot->costCenter);
         * $entity->setDocUom($snapshot->docUom);
         * $entity->setGrRow($snapshot->grRow);
         * $entity->setItem($snapshot->item);
         * $entity->setPrRow($snapshot->prRow);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setWarehouse($snapshot->warehouse);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setPoRow($snapshot->poRow);
         */

        if ($snapshot->invoice > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoice $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->find($snapshot->invoice);
            $entity->setInvoice($obj);
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

        if ($snapshot->grRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureGrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->find($snapshot->grRow);
            $entity->setGrRow($obj);
        }

        if ($snapshot->item > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->item);
            $entity->setItem($obj);
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

        if ($snapshot->poRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePoRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($snapshot->poRow);
            $entity->setPoRow($obj);
        }

        // ============
        return $entity;
    }

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     * @param \Application\Entity\FinVendorInvoice $entity
     * @return NULL|\Procure\Domain\AccountPayable\APSnapshot
     */
    public static function createSnapshot(EntityManager $doctrineEM, FinVendorInvoice $entity, $snapshot = null)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new APSnapshot();
        }

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
        $snapshot->currentStatus = $entity->getCurrentStatus();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->paymentTerm = $entity->getPaymentTerm();
        $snapshot->transactionType = $entity->getTransactionType();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->incoterm = $entity->getIncoterm();
        $snapshot->incotermPlace = $entity->getIncotermPlace();
        $snapshot->isReversed = $entity->getIsReversed();
        $snapshot->reversalDoc = $entity->getReversalDoc();
        $snapshot->reversalReason = $entity->getReversalReason();
        $snapshot->paymentStatus = $entity->getPaymentStatus();
        $snapshot->isReversable = $entity->getIsReversable();
        $snapshot->docType = $entity->getDocType();
        $snapshot->totalDocValue = $entity->getTotalDocValue();
        $snapshot->totalDocTax = $entity->getTotalDocTax();
        $snapshot->totalDocDiscount = $entity->getTotalDocDiscount();
        $snapshot->totalLocalValue = $entity->getTotalLocalValue();
        $snapshot->totalLocalTax = $entity->getTotalLocalTax();
        $snapshot->totalLocalDiscount = $entity->getTotalLocalDiscount();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->discountRate = $entity->getDiscountRate();
        $snapshot->discountAmount = $entity->getDiscountAmount();
        $snapshot->docNumber = $entity->getDocNumber();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->invoiceDate = $entity->getInvoiceDate();
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->reversalDate = $entity->getReversalDate();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->postingDate = $entity->getPostingDate();
         * $snapshot->grDate = $entity->getGrDate();
         * $snapshot->contractDate = $entity->getContractDate();
         * $snapshot->quotationDate = $entity->getQuotationDate();
         */

        if (! $entity->getInvoiceDate() == null) {
            $snapshot->invoiceDate = $entity->getInvoiceDate()->format("Y-m-d");
        }

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastchangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
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

        if (! $entity->getQuotationDate() == null) {
            $snapshot->quotationDate = $entity->getQuotationDate()->format("Y-m-d");
        }

        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $snapshot->vendor = $entity->getVendor();
         * $snapshot->procureGr = $entity->getProcureGr();
         * $snapshot->localCurrency = $entity->getLocalCurrency();
         * $snapshot->docCurrency = $entity->getDocCurrency();
         * $snapshot->postingPeriod = $entity->getPostingPeriod();
         * $snapshot->incoterm2 = $entity->getIncoterm2();
         * $snapshot->pmtTerm = $entity->getPmtTerm();
         * $snapshot->warehouse = $entity->getWarehouse();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->currency = $entity->getCurrency();
         * $snapshot->po = $entity->getPo();
         * $snapshot->company = $entity->getCompany();
         * $snapshot->paymentMethod = $entity->getPaymentMethod();
         * $snapshot->inventoryGr = $entity->getInventoryGr();
         */

        if ($entity->getVendor() !== null) {
            HeaderMapper::updateVendorDetails($snapshot, $entity->getVendor());
        }

        if ($entity->getProcureGr() !== null) {
            $snapshot->procureGr = $entity->getProcureGr()->getId();
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

        if ($entity->getIncoterm2() !== null) {
            HeaderMapper::updateIncotermDetails($snapshot, $entity->getIncoterm2());
        }
        if ($entity->getPmtTerm() !== null) {
            HeaderMapper::updatePmtTermDetails($snapshot, $entity->getPmtTerm());
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

        if ($entity->getPo() !== null) {
            $snapshot->po = $entity->getPo()->getId();
        }

        if ($entity->getCompany() !== null) {
            HeaderMapper::updateCompanyDetails($snapshot, $entity->getCompany());
        }

        if ($entity->getPaymentMethod() !== null) {
            HeaderMapper::updatePmtMethodDetails($snapshot, $entity->getPaymentMethod());
        }

        if ($entity->getInventoryGr() !== null) {
            $snapshot->inventoryGr = $entity->getInventoryGr()->getId();
        }
        return $snapshot;
    }

    /**
     *
     * @param \Doctrine\ORM\EntityManager $doctrineEM
     * @param \Application\Entity\FinVendorInvoiceRow $entity
     * @return NULL|\Procure\Domain\AccountPayable\APRowDetailsSnapshot
     */
    public static function createRowSnapshot(EntityManager $doctrineEM, FinVendorInvoiceRow $entity, $snapshot = null)
    {
        if ($entity == null)
            return null;

        if ($snapshot == null) {
            $snapshot = new APRowSnapshot();
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
        $snapshot->localUnitPrice = $entity->getLocalUnitPrice();
        $snapshot->docUnitPrice = $entity->getDocUnitPrice();
        $snapshot->exwUnitPrice = $entity->getExwUnitPrice();
        $snapshot->exwCurrency = $entity->getExwCurrency();
        $snapshot->localNetAmount = $entity->getLocalNetAmount();
        $snapshot->localGrossAmount = $entity->getLocalGrossAmount();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionType = $entity->getTransactionType();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->totalExwPrice = $entity->getTotalExwPrice();
        $snapshot->convertFactorPurchase = $entity->getConvertFactorPurchase();
        $snapshot->convertedPurchaseQuantity = $entity->getConvertedPurchaseQuantity();
        $snapshot->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $snapshot->convertedStockUnitPrice = $entity->getConvertedStockUnitPrice();
        $snapshot->convertedStandardQuantity = $entity->getConvertedStandardQuantity();
        $snapshot->convertedStandardUnitPrice = $entity->getConvertedStandardUnitPrice();
        $snapshot->docQuantity = $entity->getDocQuantity();
        $snapshot->docUnit = $entity->getDocUnit();
        $snapshot->convertedPurchaseUnitPrice = $entity->getConvertedPurchaseUnitPrice();
        $snapshot->isReversed = $entity->getIsReversed();
        $snapshot->reversalReason = $entity->getReversalReason();
        $snapshot->reversalDoc = $entity->getReversalDoc();
        $snapshot->isReversable = $entity->getIsReversable();
        $snapshot->docType = $entity->getDocType();
        $snapshot->descriptionText = $entity->getDescriptionText();
        $snapshot->vendorItemName = $entity->getVendorItemName();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->targetObject = $entity->getTargetObject();
        $snapshot->targetObjectId = $entity->getTargetObjectId();
        $snapshot->sourceObject = $entity->getSourceObject();
        $snapshot->sourceObjectId = $entity->getSourceObjectId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->standardConvertFactor = $entity->getStandardConvertFactor();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->reversalDate= $entity->getReversalDate();
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
         * $snapshot->glAccount = $entity->getGlAccount();
         * $snapshot->costCenter = $entity->getCostCenter();
         * $snapshot->docUom = $entity->getDocUom();
         * $snapshot->grRow = $entity->getGrRow();
         * $snapshot->item = $entity->getItem();
         * $snapshot->prRow = $entity->getPrRow();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->warehouse = $entity->getWarehouse();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->poRow = $entity->getPoRow();
         */

        if ($entity->getInvoice() !== null) {
            RowMapper::updateInvoiceDetails($snapshot, $entity->getInvoice()); // Parent Detail.
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

        if ($entity->getGrRow() !== null) {
            $snapshot->grRow = $entity->getGrRow()->getId();
            if ($entity->getGrRow()->getGr() !== null) {
                $snapshot->grToken = $entity->getGrRow()
                    ->getGr()
                    ->getToken();
                $snapshot->grId = $entity->getGrRow()
                    ->getGr()
                    ->getId();
            }
        }

        if ($entity->getItem() !== null) {
            RowMapper::updateItemDetails($snapshot, $entity->getItem());
        }

        if ($entity->getPrRow() !== null) {
            RowMapper::updatePRDetails($snapshot, $entity->getPrRow());
        }

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = sprintf("%s %s", $entity->getCreatedBy()->getFirstname(), $entity->getCreatedBy()->getFirstname());
        }

        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangeByName = sprintf("%s %s", $entity->getLastchangeBy()->getFirstname(), $entity->getLastchangeBy()->getFirstname());
        }

        if ($entity->getWarehouse() !== null) {
            RowMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        if ($entity->getPoRow() !== null) {
            $snapshot->poRow = $entity->getPoRow()->getId();
            if ($entity->getPoRow()->getPo() !== null) {
                $snapshot->po = $entity->getPoRow()
                    ->getPo()
                    ->getId();

                $snapshot->poId = $entity->getPoRow()
                    ->getPo()
                    ->getId();

                $snapshot->poToken = $entity->getPoRow()
                    ->getPo()
                    ->getToken();
            }
        }

        return $snapshot;
    }
}
