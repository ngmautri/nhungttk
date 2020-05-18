<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\NmtInventoryMv;
use Application\Entity\NmtInventoryTrx;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Transaction\TrxRowSnapshot;
use Inventory\Domain\Transaction\TrxSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param TrxSnapshot $snapshot
     * @param NmtInventoryMv $entity
     * @return NULL|\Application\Entity\NmtInventoryMv
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, TrxSnapshot $snapshot, NmtInventoryMv $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        // $entity->setId($snapshot->id);
        $entity->setToken($snapshot->token);
        $entity->setCurrencyIso3($snapshot->currencyIso3);
        $entity->setExchangeRate($snapshot->exchangeRate);
        $entity->setRemarks($snapshot->remarks);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setIsActive($snapshot->isActive);
        $entity->setTrxType($snapshot->trxType);
        $entity->setLastchangeBy($snapshot->lastchangeBy);
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
        $entity->setPaymentMethod($snapshot->paymentMethod);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setMovementType($snapshot->movementType);
        $entity->setJournalMemo($snapshot->journalMemo);
        $entity->setMovementFlow($snapshot->movementFlow);
        $entity->setMovementTypeMemo($snapshot->movementTypeMemo);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setReversalDoc($snapshot->reversalDoc);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setIsReversable($snapshot->isReversable);
        $entity->setDocType($snapshot->docType);
        $entity->setIsTransferTransaction($snapshot->isTransferTransaction);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setUuid($snapshot->uuid);
        $entity->setDocNumber($snapshot->docNumber);
        $entity->setDocVersion($snapshot->docVersion);
        $entity->setPmtTerm($snapshot->pmtTerm);
        $entity->setDiscountRate($snapshot->discountRate);

        // =================================
        // Mapping None-Object Field
        // =================================

        // ============================
        // DATE MAPPING
        // ============================
        // $entity->setCreatedOn($snapshot->createdOn);
        // $entity->setLastchangeOn($snapshot->lastchangeOn);
        // $entity->setPostingDate($snapshot->postingDate);
        // $entity->setContractDate($snapshot->contractDate);
        // $entity->setMovementDate($snapshot->movementDate);
        // $entity->setReversalDate($snapshot->reversalDate);
        // $entity->setDocDate($snapshot->docDate);

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        if ($snapshot->postingDate !== null) {
            $entity->setPostingDate(new \DateTime($snapshot->postingDate));
        }

        if ($snapshot->contractDate !== null) {
            $entity->setContractDate(new \DateTime($snapshot->contractDate));
        }

        if ($snapshot->movementDate !== null) {
            $entity->setMovementDate(new \DateTime($snapshot->movementDate));
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
        // $entity->setCreatedBy($snapshot->createdBy);
        // $entity->setCompany($snapshot->company);
        // $entity->setVendor($snapshot->vendor);
        // $entity->setWarehouse($snapshot->warehouse);
        // $entity->setTargetWarehouse($snapshot->targetWarehouse);

        // $entity->setPostingPeriod($snapshot->postingPeriod);
        // $entity->setCurrency($snapshot->currency);
        // $entity->setDocCurrency($snapshot->docCurrency);
        // $entity->setLocalCurrency($snapshot->localCurrency);
        // $entity->setSourceLocation($snapshot->sourceLocation);
        // $entity->setTartgetLocation($snapshot->tartgetLocation);

        // =========

        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        if ($snapshot->vendor > 0) {
            /**
             *
             * @var \Application\Entity\NmtBpVendor $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($snapshot->vendor);

            $entity->setVendor($obj);
        }

        if ($snapshot->warehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->warehouse);
            $entity->setWarehouse($obj);
        }

        if ($snapshot->targetWarehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->targetWarehouse);
            $entity->setTargetWarehouse($obj);
        }

        if ($snapshot->postingPeriod > 0) {
            /**
             *
             * @var \Application\Entity\NmtFinPostingPeriod $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->find($snapshot->postingPeriod);
            $entity->setPostingPeriod($obj);
        }

        if ($snapshot->currency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->currency);
            $entity->setCurrency($obj);
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

        if ($snapshot->sourceLocation > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouseLocation $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->find($snapshot->sourceLocation);
            $entity->setSourceLocation($obj);
        }

        if ($snapshot->tartgetLocation > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouseLocation $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->find($snapshot->tartgetLocation);
            $entity->setTartgetLocation($obj);
        }

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param TrxRowSnapshot $snapshot
     * @param NmtInventoryTrx $entity
     */
    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, TrxRowSnapshot $snapshot, NmtInventoryTrx $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        // $entity->setId($snapshot->id);
        $entity->setToken($snapshot->token);
        $entity->setChecksum($snapshot->checksum);
        $entity->setTrxTypeId($snapshot->trxTypeId);
        $entity->setFlow($snapshot->flow);
        $entity->setQuantity($snapshot->quantity);
        $entity->setRemarks($snapshot->remarks);
        $entity->setIsLocked($snapshot->isLocked);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setIsActive($snapshot->isActive);
        $entity->setIsPreferredVendor($snapshot->isPreferredVendor);
        $entity->setVendorItemUnit($snapshot->vendorItemUnit);
        $entity->setVendorItemCode($snapshot->vendorItemCode);
        $entity->setConversionFactor($snapshot->conversionFactor);
        $entity->setConversionText($snapshot->conversionText);
        $entity->setVendorUnitPrice($snapshot->vendorUnitPrice);
        $entity->setPmtTermId($snapshot->pmtTermId);
        $entity->setDeliveryTermId($snapshot->deliveryTermId);
        $entity->setLeadTime($snapshot->leadTime);
        $entity->setTaxRate($snapshot->taxRate);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setCurrentStatus($snapshot->currentStatus);
        $entity->setTargetId($snapshot->targetId);
        $entity->setTargetClass($snapshot->targetClass);
        $entity->setSourceId($snapshot->sourceId);
        $entity->setSourceClass($snapshot->sourceClass);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setSysNumber($snapshot->sysNumber);
        $entity->setChangeBy($snapshot->changeBy);
        $entity->setRevisionNumber($snapshot->revisionNumber);
        $entity->setIsPosted($snapshot->isPosted);
        $entity->setActualQuantity($snapshot->actualQuantity);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setStockRemarks($snapshot->stockRemarks);
        $entity->setTransactionType($snapshot->transactionType);
        $entity->setItemSerialId($snapshot->itemSerialId);
        $entity->setItemBatchId($snapshot->itemBatchId);
        $entity->setCogsLocal($snapshot->cogsLocal);
        $entity->setCogsDoc($snapshot->cogsDoc);
        $entity->setExchangeRate($snapshot->exchangeRate);
        $entity->setConvertedStandardQuantity($snapshot->convertedStandardQuantity);
        $entity->setConvertedStandardUnitPrice($snapshot->convertedStandardUnitPrice);
        $entity->setConvertedStockQuantity($snapshot->convertedStockQuantity);
        $entity->setConvertedStockUnitPrice($snapshot->convertedStockUnitPrice);
        $entity->setConvertedPurchaseQuantity($snapshot->convertedPurchaseQuantity);
        $entity->setDocQuantity($snapshot->docQuantity);
        $entity->setDocUnitPrice($snapshot->docUnitPrice);
        $entity->setDocUnit($snapshot->docUnit);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setReversalDoc($snapshot->reversalDoc);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setIsReversable($snapshot->isReversable);
        $entity->setDocType($snapshot->docType);
        $entity->setLocalUnitPrice($snapshot->localUnitPrice);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setMvUuid($snapshot->mvUuid);
        $entity->setStandardConvertFactor($snapshot->standardConvertFactor);
        $entity->setGlAccount($snapshot->glAccount);
        $entity->setLocalGrossAmount($snapshot->localGrossAmount);
        $entity->setLocalNetAmount($snapshot->localNetAmount);
        $entity->setUuid($snapshot->uuid);
        $entity->setDocVersion($snapshot->docVersion);
        $entity->setVendorItemName($snapshot->vendorItemName);
        $entity->setRowIdentifier($snapshot->rowIdentifier);
        $entity->setDiscountRate($snapshot->discountRate);
        $entity->setDiscountAmount($snapshot->discountAmount);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setLastchangeOn($snapshot->lastchangeOn);
        $entity->setUnit($snapshot->unit);
        $entity->setGrossAmount($snapshot->grossAmount);
        $entity->setNetAmount($snapshot->netAmount);
        $entity->setConvertFactorPuchase($snapshot->convertFactorPuchase);
        $entity->setConvertedPurchaseUnitPrice($snapshot->convertedPurchaseUnitPrice);
        $entity->setLastchangeBy($snapshot->lastchangeBy);
        $entity->setInvoiceId($snapshot->invoiceId);
        $entity->setLastChangeBy($snapshot->lastChangeBy);

        // ============================
        // DATE MAPPING
        // ============================
        // $entity->setTrxDate($snapshot->trxDate);
        // $entity->setCreatedOn($snapshot->createdOn);
        // $entity->setLastChangeOn($snapshot->lastChangeOn);
        // $entity->setChangeOn($snapshot->changeOn);
        // $entity->setReversalDate($snapshot->reversalDate);

        if ($snapshot->trxDate !== null) {
            $entity->setTrxDate(new \DateTime($snapshot->trxDate));
        }

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        if ($snapshot->reversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->reversalDate));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        // $entity->setCreatedBy($snapshot->createdBy);
        // $entity->setLastChangeBy($snapshot->lastChangeBy);
        // $entity->setItem($snapshot->item);
        // $entity->setPr($snapshot->pr);
        // $entity->setPo($snapshot->po);
        // $entity->setVendorInvoice($snapshot->vendorInvoice);
        // $entity->setPoRow($snapshot->poRow);
        // $entity->setGrRow($snapshot->grRow);
        // $entity->setInventoryGi($snapshot->inventoryGi);
        // $entity->setInventoryGr($snapshot->inventoryGr);
        // $entity->setInventoryTransfer($snapshot->inventoryTransfer);

        // $entity->setWh($snapshot->wh);
        // $entity->setGr($snapshot->gr);

        // $entity->setMovement($snapshot->movement);
        // $entity->setIssueFor($snapshot->issueFor);
        // $entity->setDocCurrency($snapshot->docCurrency);
        // $entity->setLocalCurrency($snapshot->localCurrency);
        // $entity->setProject($snapshot->project);
        // $entity->setCostCenter($snapshot->costCenter);
        // $entity->setDocUom($snapshot->docUom);
        // $entity->setPostingPeriod($snapshot->postingPeriod);
        // $entity->setWhLocation($snapshot->whLocation);
        // $entity->setWarehouse($snapshot->warehouse);
        // $entity->setPrRow($snapshot->prRow);
        // $entity->setVendor($snapshot->vendor);
        // $entity->setCurrency($snapshot->currency);
        // $entity->setPmtMethod($snapshot->pmtMethod);
        // $entity->setInvoiceRow($snapshot->invoiceRow);

        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        if ($snapshot->item > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->item);
            $entity->setItem($obj);
        }

        if ($snapshot->pr > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePr $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePr')->find($snapshot->pr);
            $entity->setPr($obj);
        }

        if ($snapshot->po > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePo $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePo')->find($snapshot->po);
            $entity->setPo($obj);
        }

        if ($snapshot->vendorInvoice > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoice $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->find($snapshot->po);
            $entity->setVendorInvoice($obj);
        }

        if ($snapshot->poRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePoRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($snapshot->poRow);
            $entity->setPoRow($obj);
        }

        if ($snapshot->grRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureGrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->find($snapshot->grRow);
            $entity->setGrRow($obj);
        }

        if ($snapshot->gr > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureGr $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureGr')->find($snapshot->gr);
            $entity->setGr($obj);
        }

        if ($snapshot->movement > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryMv $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryMv')->find($snapshot->movement);
            $entity->setMovement($obj);
        }

        if ($snapshot->issueFor > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->issueFor);
            $entity->setIssueFor($obj);
        }

        if ($snapshot->docCurrency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency$obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->docCurrency);
            $entity->setDocCurrency($obj);
        }

        if ($snapshot->localCurrency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency$obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->localCurrency);
            $entity->setLocalCurrency($obj);
        }

        if ($snapshot->project > 0) {
            /**
             *
             * @var \Application\Entity\NmtPmProject $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtPmProject')->find($snapshot->project);
            $entity->setProject($obj);
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

        if ($snapshot->postingPeriod > 0) {
            /**
             *
             * @var \Application\Entity\NmtFinPostingPeriod $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->find($snapshot->postingPeriod);
            $entity->setPostingPeriod($obj);
        }

        if ($snapshot->warehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->warehouse);
            $entity->setWarehouse($obj);
            $entity->setWh($obj);
        }

        if ($snapshot->wh > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->wh);
            $entity->setWh($obj);
        }

        if ($snapshot->prRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($snapshot->prRow);
            $entity->setPrRow($obj);
        }

        if ($snapshot->vendor > 0) {
            /**
             *
             * @var \Application\Entity\NmtBpVendor $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtBpVendor')->find($snapshot->vendor);
            $entity->setVendor($obj);
        }

        if ($snapshot->currency > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCurrency $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($snapshot->currency);
            $entity->setCurrency($obj);
        }

        if ($snapshot->pmtMethod > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationPmtTerm $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->find($snapshot->pmtMethod);
            $entity->setPmtMethod($obj);
        }

        if ($snapshot->invoiceRow > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoiceRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->find($snapshot->invoiceRow);
            $entity->setInvoiceRow($obj);
        }

        // ============
        return $entity;
    }
}
