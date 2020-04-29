<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\NmtProcurePr;
use Application\Entity\NmtProcureQo;
use Application\Entity\NmtProcureQoRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Domain\QuotationRequest\QRRowSnapshot;
use Procure\Domain\QuotationRequest\QRSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PRSnapshot $snapshot
     * @param NmtProcurePr $entity
     * @return NULL|\Application\Entity\NmtProcurePr
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, QRSnapshot $snapshot, NmtProcureQo $entity)
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
        $entity->setQuotationDate($snapshot->quotationDate);
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
        $entity->setTotalDocValue($snapshot->totalDocValue);
        $entity->setTotalDocTax($snapshot->totalDocTax);
        $entity->setTotalDocDiscount($snapshot->totalDocDiscount);
        $entity->setTotalLocalValue($snapshot->totalLocalValue);
        $entity->setTotalLocalTax($snapshot->totalLocalTax);
        $entity->setTotalLocalDiscount($snapshot->totalLocalDiscount);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setUuid($snapshot->uuid);
        $entity->setDocNumber($snapshot->docNumber);
        $entity->setDocVersion($snapshot->docVersion);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setCurrentStatus($snapshot->currentStatus);
        $entity->setPmtTerm($snapshot->pmtTerm);
        $entity->setDiscountRate($snapshot->discountRate);
        $entity->setDiscountAmount($snapshot->discountAmount);
        $entity->setContractNo($snapshot->contractNo);

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         * $entity->setPostingDate($snapshot->postingDate);
         * $entity->setGrDate($snapshot->grDate);
         * $entity->setContractDate($snapshot->contractDate);
         * $entity->setQuotationDate($snapshot->quotationate);
         * $entity->setDocDate($snapshot->docDate);
         * $entity->setInvoiceDate($snapshot->invoiceDate);
         */

        if (! $entity->getInvoiceDate() == null) {
            $snapshot->invoiceDate = $entity->getInvoiceDate()->format("Y-m-d");
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

        if ($snapshot->docDate !== null) {
            $entity->setDocDate(new \DateTime($snapshot->docDate));
        }

        if ($snapshot->invoiceDate !== null) {
            $entity->setInvoiceDate(new \DateTime($snapshot->invoiceDate));
        }

        // Overwrite.
        $entity->setQuotationNo($snapshot->getDocNumber());
        $entity->setQuotationDate($entity->getDocDate());

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setVendor($snapshot->vendor);
         * $entity->setIncoterm2($snapshot->incoterm2);
         * $entity->setWarehouse($snapshot->warehouse);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setCurrency($snapshot->currency);
         * $entity->setPaymentMethod($snapshot->paymentMethod);
         * $entity->setCompany($snapshot->company);
         * $entity->setLocalCurrency($snapshot->localCurrency);
         * $entity->setDocCurrency($snapshot->docCurrency);
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

        if ($snapshot->incoterm2 > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationIncoterms $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationIncoterms')->find($snapshot->incoterm2);
            $entity->setIncoterm2($obj);
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

        if ($snapshot->paymentMethod > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationPmtMethod $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationPmtMethod')->find($snapshot->paymentMethod);
            $entity->setPaymentMethod($obj);
        }

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
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

        return $entity;
    }

    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, QRRowSnapshot $snapshot, NmtProcureQoRow $entity)
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
        $entity->setTargetObject($snapshot->targetObject);
        $entity->setSourceObject($snapshot->sourceObject);
        $entity->setTargetObjectId($snapshot->targetObjectId);
        $entity->setSourceObjectId($snapshot->sourceObjectId);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setDocQuantity($snapshot->docQuantity);
        $entity->setDocUnitPrice($snapshot->docUnitPrice);
        $entity->setDocUnit($snapshot->docUnit);
        $entity->setDocUom($snapshot->docUom);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setDocType($snapshot->docType);
        $entity->setDescriptionText($snapshot->descriptionText);
        $entity->setVendorItemName($snapshot->vendorItemName);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setUuid($snapshot->uuid);
        $entity->setDocVersion($snapshot->docVersion);

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         */
        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setInvoice($snapshot->invoice);
         * $entity->setPrRow($snapshot->prRow);
         * $entity->setWarehouse($snapshot->warehouse);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setQo($snapshot->qo);
         * $entity->setItem($snapshot->item);
         */

        if ($snapshot->invoice > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoice $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->find($snapshot->invoice);
            $entity->setInvoice($obj);
        }

        if ($snapshot->prRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($snapshot->prRow);
            $entity->setPrRow($obj);
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

        if ($snapshot->qo > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureQo $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureQo')->find($snapshot->qo);
            $entity->setQo($obj);
        }

        if ($snapshot->item > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->item);
            $entity->setItem($obj);
        }

        return $entity;
    }

    public static function createSnapshot(EntityManager $doctrineEM, NmtProcureQo $entity)
    {
        if ($entity == null || $doctrineEM == null) {
            return null;
        }

        $snapshot = new QRSnapshot();

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
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->docType = $entity->getDocType();
        $snapshot->totalDocValue = $entity->getTotalDocValue();
        $snapshot->totalDocTax = $entity->getTotalDocTax();
        $snapshot->totalDocDiscount = $entity->getTotalDocDiscount();
        $snapshot->totalLocalValue = $entity->getTotalLocalValue();
        $snapshot->totalLocalTax = $entity->getTotalLocalTax();
        $snapshot->totalLocalDiscount = $entity->getTotalLocalDiscount();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->docNumber = $entity->getDocNumber();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->currentStatus = $entity->getCurrentStatus();
        $snapshot->pmtTerm = $entity->getPmtTerm();
        $snapshot->discountRate = $entity->getDiscountRate();
        $snapshot->discountAmount = $entity->getDiscountAmount();

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
         * $snapshot->docDate = $entity->getDocDate();
         */

        if (! $entity->getInvoiceDate() == null) {
            $snapshot->invoiceDate = $entity->getInvoiceDate()->format("Y-m-d");
        }
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
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
         * $snapshot->incoterm2 = $entity->getIncoterm2();
         * $snapshot->warehouse = $entity->getWarehouse();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->currency = $entity->getCurrency();
         * $snapshot->paymentMethod = $entity->getPaymentMethod();
         * $snapshot->company = $entity->getCompany();
         * $snapshot->localCurrency = $entity->getLocalCurrency();
         * $snapshot->docCurrency = $entity->getDocCurrency();
         */

        if ($entity->getVendor() !== null) {
            HeaderMapper::updateVendorDetails($snapshot, $entity->getVendor());
        }
        if ($entity->getIncoterm2() !== null) {
            HeaderMapper::updateIncotermDetails($snapshot, $entity->getIncoterm2());
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

        if ($entity->getPaymentMethod() !== null) {
            HeaderMapper::updatePmtMethodDetails($snapshot, $entity->getPaymentMethod());
        }
        if ($entity->getCompany() !== null) {
            HeaderMapper::updateCompanyDetails($snapshot, $entity->getCompany());
        }

        if ($entity->getLocalCurrency() !== null) {
            HeaderMapper::updateLocalCurrencyDetails($snapshot, $entity->getLocalCurrency());
        }

        if ($entity->getDocCurrency() !== null) {
            HeaderMapper::updateDocCurrencyDetails($snapshot, $entity->getDocCurrency());
        }

        return $snapshot;
    }

    public static function createRowSnapshot(EntityManager $doctrineEM, NmtProcureQoRow $entity)
    {
        if ($entity == null || $doctrineEM == null) {
            return null;
        }

        $snapshot = new QRRowSnapshot();

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
        $snapshot->docQuantity = $entity->getDocQuantity();
        $snapshot->docUnitPrice = $entity->getDocUnitPrice();
        $snapshot->docUnit = $entity->getDocUnit();
        $snapshot->docUom = $entity->getDocUom();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->docType = $entity->getDocType();
        $snapshot->descriptionText = $entity->getDescriptionText();
        $snapshot->vendorItemName = $entity->getVendorItemName();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->docVersion = $entity->getDocVersion();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $snapshot->invoice = $entity->getInvoice();
         * $snapshot->prRow = $entity->getPrRow();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->warehouse = $entity->getWarehouse();
         * $snapshot->qo = $entity->getQo();
         * $snapshot->item = $entity->getItem();
         */

        if ($entity->getInvoice() !== null) {
            $snapshot->invoice = $entity->getInvoice()->getId();
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

        // Parent Detail
        if ($entity->getQo() !== null) {

            $snapshot->qo = $entity->getQo()->getId();
            RowMapper::updateQuoteDetails($snapshot, $entity->getQo());
        }

        if ($entity->getItem() !== null) {
            RowMapper::updateItemDetails($snapshot, $entity->getItem());
        }

        return $snapshot;
    }
}
