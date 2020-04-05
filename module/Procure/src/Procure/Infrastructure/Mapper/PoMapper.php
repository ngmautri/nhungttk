<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\NmtProcurePo;
use Application\Entity\NmtProcurePoRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\PurchaseOrder\PODetailsSnapshot;
use Procure\Domain\PurchaseOrder\PORowDetailsSnapshot;
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
        $entity->setInvoiceNo($snapshot->invoiceNo);
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

        $entity->setDocVersion($snapshot->docVersion); // new

        // DATE MAPPING
        // $entity->setCreatedOn($snapshot->createdOn);
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        // $entity->setInvoiceDate($snapshot->invoiceDate);
        if ($snapshot->invoiceDate !== null) {
            $entity->setInvoiceDate(new \DateTime($snapshot->invoiceDate));
        }

        // $entity->setLastchangeOn($snapshot->lastchangeOn);
        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        // $entity->setPostingDate($snapshot->postingDate);
        if ($snapshot->postingDate !== null) {
            $entity->setPostingDate(new \DateTime($snapshot->postingDate));
        }

        // $entity->setGrDate($snapshot->grDate);
        if ($snapshot->grDate !== null) {
            $entity->setGrDate(new \DateTime($snapshot->grDate));
        }

        // $entity->setContractDate($snapshot->contractDate);
        if ($snapshot->contractDate !== null) {
            $entity->setContractDate(new \DateTime($snapshot->contractDate));
        }

        // $entity->setQuotationDate($snapshot->quotationDate);
        if ($snapshot->quotationDate !== null) {
            $entity->setQuotationDate(new \DateTime($snapshot->quotationDate));
        }

        // REFERRENCE MAPPING

        // $entity->setVendor($snapshot->vendor);
        if ($snapshot->vendor > 0) {
            /**
             *
             * @var \Application\Entity\NmtBpVendor $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($snapshot->vendor);

            $entity->setVendor($obj);
            $entity->setVendorName($obj->getVendorName());
        }

        // $entity->setPmtTerm($snapshot->pmtTerm);
        if ($snapshot->pmtTerm > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationPmtTerm $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->find($snapshot->pmtTerm);
            $entity->setPmtTerm($obj);
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
        // $entity->setPaymentMethod($snapshot->paymentMethod);
        if ($snapshot->paymentMethod > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationPmtMethod $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationPmtMethod')->find($snapshot->paymentMethod);
            $entity->setPaymentMethod($obj);
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
            $entity->setCurrencyIso3($obj->getCurrency());
        }

        // $entity->setIncoterm2($snapshot->incoterm2);
        if ($snapshot->incoterm2 > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationIncoterms $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationIncoterms')->find($snapshot->incoterm2);
            $entity->setIncoterm2($obj);
        }

        return $entity;
    }

    /**
     *
     * @param PORowSnapshot $snapshot
     * @param NmtProcurePoRow $entity
     * @return NULL|\Application\Entity\NmtProcurePoRow
     */
    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, PORowSnapshot $snapshot, NmtProcurePoRow $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

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

        // DATE MAPPING

        // $entity->setLastchangeOn($snapshot->lastchangeOn);
        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        // $entity->setCreatedOn($snapshot->createdOn);
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        // REFERRENCE MAPPING

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
     * @param object $entity
     * @return NULL|\Procure\Domain\PurchaseOrder\PODetailsSnapshot
     */
    public static function createDetailSnapshot(NmtProcurePo $entity = null)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new PODetailsSnapshot();

        // Mapping Reference
        // =====================

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
            $snapshot->companyName = $entity->getCompany()->getCompanyName();
            $snapshot->companyCode = $entity->getCompany()->getCompanyCode();
            $snapshot->companyToken = $entity->getCompany()->getToken();
        }

        // $snapshot->vendor= $entity->getVendor();
        if ($entity->getVendor() !== null) {
            $snapshot->vendor = $entity->getVendor()->getId();
            $snapshot->vendorName = $entity->getVendor()->getVendorName();
            $snapshot->vendorId = $entity->getVendor()->getId();
            $snapshot->vendorToken = $entity->getVendor()->getToken();
            $snapshot->vendorAddress = sprintf("%s %s",$entity->getVendor()->getStreet() , $entity->getVendor()->getCity());

            if ($entity->getVendor()->getCountry() !== null) {
                $snapshot->vendorCountry = $entity->getVendor()
                    ->getCountry()
                    ->getCountryName();
            }
        }

        // $snapshot->pmtTerm = $entity->getPmtTerm();
        if ($entity->getPmtTerm() !== null) {
            $snapshot->pmtTerm = $entity->getPmtTerm()->getId();
            $snapshot->paymentTermName = $entity->getPmtTerm()->getPmtTermName();
            $snapshot->paymentTermCode = $entity->getPmtTerm()->getPmtTermCode();
        }

        // $snapshot->warehouse = $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
            $snapshot->warehouseName = $entity->getWarehouse()->getWhName();
            $snapshot->warehouseCode = $entity->getWarehouse()->getWhCode();
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
            $snapshot->paymentMethod = $entity->getPaymentMethod()->getId();
            $snapshot->paymentMethodName = $entity->getPaymentMethod()->getMethodName();
            $snapshot->paymentMethodCode = $entity->getPaymentMethod()->getMethodCode();
        }

        // $snapshot->localCurrency = $entity->getLocalCurrency();
        if ($entity->getLocalCurrency() !== null) {
            $snapshot->localCurrency = $entity->getLocalCurrency()->getId();
            $snapshot->localCurrencyId = $snapshot->localCurrency;
            $snapshot->localCurrencyISO = $entity->getLocalCurrency()->getCurrency();
        }

        // $snapshot->docCurrency = $entity->getDocCurrency();
        if ($entity->getDocCurrency() !== null) {
            $snapshot->docCurrency = $entity->getDocCurrency()->getId();
            $snapshot->currencyIso3 = $entity->getDocCurrency()->getCurrency();
            $snapshot->docCurrencyId = $snapshot->docCurrency;
            $snapshot->docCurrencyISO = $snapshot->currencyIso3;
        }

        // $snapshot->incoterm2 = $entity->getIncoterm2();
        if ($entity->getIncoterm2() !== null) {
            $snapshot->incoterm2 = $entity->getIncoterm2()->getId();
            $snapshot->incotermCode = $entity->getIncoterm2()->getIncoterm();
            $snapshot->incotermName = $entity->getIncoterm2()->getIncoterm1();
        }

        // MAPPING DATE
        // =====================
        $snapshot->invoiceDate = $entity->getInvoiceDate();
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
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        // $snapshot->lastchangeOn = $entity->getLastchangeOn();
        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // $snapshot->contractDate= $entity->getContractDate();
        if (! $entity->getContractDate() == null) {
            $snapshot->contractDate = $entity->getContractDate()->format("Y-m-d");
        }

        // Mapping None-Object Field
        // =====================

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
        $snapshot->docVersion = $entity->getDocVersion(); // new/

        return $snapshot;
    }

    /**
     *
     * @param NmtProcurePoRow $entity
     * @return NULL|\Procure\Domain\PurchaseOrder\PORowDetailsSnapshot
     */
    public static function createRowDetailSnapshot(NmtProcurePoRow $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new PORowDetailsSnapshot();

        // Mapping Reference
        // =====================

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
            $snapshot->prRow = $entity->getPrRow()->getId();
            $snapshot->prRowIndentifer = $entity->getPrRow()->getRowIdentifer();
            $snapshot->prRowCode = $entity->getPrRow()->getRowCode();
            $snapshot->prRowName = $entity->getPrRow()->getRowName();
            $snapshot->prRowConvertFactor = $entity->getPrRow()->getConversionFactor();
            $snapshot->prRowUnit = $entity->getPrRow()->getRowUnit();

            if ($entity->getPrRow()->getPr() !== null) {
                $snapshot->pr = $entity->getPrRow()
                    ->getPr()
                    ->getId();

                $snapshot->prSysNumber = $entity->getPrRow()
                    ->getPr()
                    ->getPrAutoNumber();

                $snapshot->prNumber = $entity->getPrRow()
                    ->getPr()
                    ->getPrNumber();

                $snapshot->prToken = $entity->getPrRow()
                    ->getPr()
                    ->getToken();

                $snapshot->prChecksum = $entity->getPrRow()
                    ->getPr()
                    ->getChecksum();
            }
        }

        // $snapshot->createdBy= $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = sprintf("%s %s", $entity->getCreatedBy()->getFirstname(), $entity->getCreatedBy()->getFirstname());
        }

        // $snapshot->warehouse= $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
            $snapshot->warehouseCode = $entity->getWarehouse()->getWhCode();
            $snapshot->warehouseName = $entity->getWarehouse()->getWhName();
            $snapshot->docWarehouseCode = $snapshot->warehouseCode;
            $snapshot->docWarehouseName = $snapshot->warehouseName;
        }

        // $snapshot->po= $entity->getPo();
        if ($entity->getPo() !== null) {
            $snapshot->po = $entity->getPo()->getId();
            $snapshot->vendorName = $entity->getPo()->getVendorName();
            $snapshot->docCurrencyISO = $entity->getPo()->getCurrencyIso3();

            $snapshot->docId = $snapshot->po;
            $snapshot->docToken = $entity->getPo()->getToken();

            $snapshot->exchangeRate = $entity->getPo()->getExchangeRate();
            $snapshot->docNumber = $entity->getPo()->getContractNo();

            if ($entity->getPo()->getDocCurrency() !== null) {
                $snapshot->docCurrencyId = $entity->getPo()
                    ->getDocCurrency()
                    ->getId();
                $snapshot->docCurrencyISO = $entity->getPo()
                    ->getDocCurrency()
                    ->getCurrency();
            }

            if ($entity->getPo()->getLocalCurrency() !== null) {
                $snapshot->localCurrencyId = $entity->getPo()
                    ->getLocalCurrency()
                    ->getId();
                $snapshot->localCurrencyISO = $entity->getPo()
                    ->getLocalCurrency()
                    ->getCurrency();
            }

            if ($entity->getPo()->getVendor() !== null) {
                $snapshot->vendorId = $entity->getPo()
                    ->getVendor()
                    ->getId();
                $snapshot->vendorToken = $entity->getPo()
                    ->getVendor()
                    ->getToken();

                if ($entity->getPo()
                    ->getVendor()
                    ->getCountry() !== null) {
                    $snapshot->vendorCountry = $entity->getPo()
                        ->getVendor()
                        ->getCountry()
                        ->getCountryName();
                }
            }
        }

        // $snapshot->item= $entity->getItem();
        if ($entity->getItem() !== null) {
            $snapshot->item = $entity->getItem()->getId();
            $snapshot->itemToken = $entity->getItem()->getToken();
            $snapshot->itemName = $entity->getItem()->getItemName();
            $snapshot->itemName1 = $entity->getItem()->getItemNameForeign();

            $snapshot->itemSKU = $entity->getItem()->getItemSku();

            $snapshot->itemSKU1 = $entity->getItem()->getItemSku1();
            $snapshot->itemSKU2 = $entity->getItem()->getItemSku2();

            $snapshot->itemChecksum = $entity->getItem()->getChecksum();

            $snapshot->itemVersion = $entity->getItem()->getRevisionNo();

            if ($entity->getItem()->getStandardUom() != null) {
                $snapshot->itemStandardUnit = $entity->getItem()
                    ->getStandardUom()
                    ->getId();
                $snapshot->itemStandardUnitName = $entity->getItem()
                    ->getStandardUom()
                    ->getUomName();

                $snapshot->itemStandardUnitCode = $entity->getItem()
                    ->getStandardUom()
                    ->getUomCode();
            }
        }

        // $snapshot->docUom = $entity->getDocUom();
        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
            $snapshot->docUomCode = $entity->getDocUom()->getUomCode();
            $snapshot->docUomName = $entity->getDocUom()->getUomName();
        }

        // Mapping Date
        // =====================

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // Mapping None-Object Field
        // =====================

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

        return $snapshot;
    }
}
