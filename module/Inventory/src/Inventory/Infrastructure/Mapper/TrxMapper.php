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
        $entity->setRelevantMovementId($snapshot->getRelevantMovementId());

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

        if ($snapshot->contractDate !== null) {
            $entity->setContractDate(new \DateTime($snapshot->contractDate));
        }

        if ($snapshot->postingDate !== null) {
            $entity->setPostingDate(new \DateTime($snapshot->postingDate));

            // set Posting = Movement Date
            if ($snapshot->movementDate == null) {
                $entity->setMovementDate(new \DateTime($snapshot->postingDate));
            }
        }

        if ($snapshot->movementDate !== null) {
            $entity->setMovementDate(new \DateTime($snapshot->movementDate));
            $entity->setPostingDate(new \DateTime($snapshot->movementDate));
        }

        if ($snapshot->reversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->reversalDate));
        }

        if ($snapshot->docDate !== null) {
            $entity->setDocDate(new \DateTime($snapshot->docDate));
        }

        if ($snapshot->quotationDate !== null) {
            $entity->setQuotationDate(new \DateTime($snapshot->quotationDate));
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
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->find($snapshot->vendorInvoice);
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

        if ($snapshot->whLocation > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouseLocation $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->find($snapshot->whLocation);
            $entity->setWhLocation($obj);
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

    /**
     *
     * @param EntityManager $doctrineEM
     * @param NmtInventoryMv $entity
     * @param object $snapshot
     * @return NULL|string|\Inventory\Domain\Transaction\TrxSnapshot
     */
    public static function createSnapshot(EntityManager $doctrineEM, NmtInventoryMv $entity, $snapshot = null)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new TrxSnapshot();
        }

        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->currencyIso3 = $entity->getCurrencyIso3();
        $snapshot->exchangeRate = $entity->getExchangeRate();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->trxType = $entity->getTrxType();
        $snapshot->lastchangeBy = $entity->getLastchangeBy();
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
        $snapshot->movementType = $entity->getMovementType();
        $snapshot->journalMemo = $entity->getJournalMemo();
        $snapshot->movementFlow = $entity->getMovementFlow();
        $snapshot->movementTypeMemo = $entity->getMovementTypeMemo();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->isReversed = $entity->getIsReversed();
        $snapshot->reversalDoc = $entity->getReversalDoc();
        $snapshot->reversalReason = $entity->getReversalReason();
        $snapshot->isReversable = $entity->getIsReversable();
        $snapshot->docType = $entity->getDocType();
        $snapshot->isTransferTransaction = $entity->getIsTransferTransaction();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->docNumber = $entity->getDocNumber();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->pmtTerm = $entity->getPmtTerm();
        $snapshot->discountRate = $entity->getDiscountRate();
        $snapshot->relevantMovementId = $entity->getRelevantMovementId();

        // =================================
        // Mapping None-Object Field
        // =================================

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->postingDate = $entity->getPostingDate();
         * $snapshot->contractDate = $entity->getContractDate();
         * $snapshot->quotationDate = $entity->getQuotationDate();
         * $snapshot->movementDate = $entity->getMovementDate();
         * $snapshot->reversalDate = $entity->getReversalDate();
         * $snapshot->docDate = $entity->getDocDate();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getPostingDate() == null) {
            $snapshot->postingDate = $entity->getPostingDate()->format("Y-m-d");
        }

        if (! $entity->getContractDate() == null) {
            $snapshot->contractDate = $entity->getContractDate()->format("Y-m-d");
        }

        if (! $entity->getQuotationDate() == null) {
            $snapshot->quotationDate = $entity->getQuotationDate()->format("Y-m-d");
        }

        if (! $entity->getMovementDate() == null) {
            $snapshot->movementDate = $entity->getMovementDate()->format("Y-m-d");
        }

        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
        }

        if (! $entity->getReversalDate() == null) {
            $snapshot->docDate = $entity->getReversalDate()->format("Y-m-d");
        }

        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d H:i:s");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        // $snapshot->createdBy = $entity->getCreatedBy();
        // $snapshot->company = $entity->getCompany();
        // $snapshot->vendor = $entity->getVendor();
        // $snapshot->warehouse = $entity->getWarehouse();
        // $snapshot->postingPeriod = $entity->getPostingPeriod();
        // $snapshot->currency = $entity->getCurrency();
        // $snapshot->docCurrency = $entity->getDocCurrency();
        // $snapshot->localCurrency = $entity->getLocalCurrency();
        // snapshot->targetWarehouse = $entity->getTargetWarehouse();
        // $snapshot->sourceLocation = $entity->getSourceLocation();
        // $snapshot->tartgetLocation = $entity->getTartgetLocation();

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        if ($entity->getVendor() !== null) {
            HeaderMapper::updateVendorDetails($snapshot, $entity->getVendor());
        }

        if ($entity->getWarehouse() !== null) {
            HeaderMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        if ($entity->getPostingPeriod() !== null) {
            HeaderMapper::updatePostingPeriodDetails($snapshot, $entity->getPostingPeriod());
        }

        if ($entity->getCurrency() !== null) {
            $snapshot->currency = $entity->getCurrency()->getId();
        }

        if ($entity->getDocCurrency() !== null) {
            $snapshot->docCurrency = $entity->getDocCurrency()->getId();
        }

        if ($entity->getLocalCurrency() !== null) {
            $snapshot->localCurrency = $entity->getLocalCurrency()->getId();
        }

        if ($entity->getTargetWarehouse() !== null) {
            $snapshot->targetWarehouse = $entity->getTargetWarehouse()->getId();
        }

        if ($entity->getSourceLocation() !== null) {
            $snapshot->sourceLocation = $entity->getSourceLocation()->getId();
        }

        if ($entity->getTartgetLocation() !== null) {
            $snapshot->tartgetLocation = $entity->getTartgetLocation()->getId();
        }

        return $snapshot;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param NmtInventoryTrx $entity
     * @param object $snapshot
     * @param boolean $lazyLoading
     * @return NULL|\Inventory\Domain\Transaction\TrxRowSnapshot|string|\Inventory\Domain\Transaction\TrxRowSnapshot
     */
    public static function createRowSnapshot(EntityManager $doctrineEM, NmtInventoryTrx $entity, $snapshot = null, $lazyLoading = false)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new TrxRowSnapshot();
        }

        if ($lazyLoading) {
            $snapshot->id = $entity->getId();
            $snapshot->token = $entity->getToken();
            return $snapshot;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->checksum = $entity->getChecksum();
        $snapshot->trxTypeId = $entity->getTrxTypeId();
        $snapshot->flow = $entity->getFlow();
        $snapshot->quantity = $entity->getQuantity();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->isLocked = $entity->getIsLocked();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->isPreferredVendor = $entity->getIsPreferredVendor();
        $snapshot->vendorItemUnit = $entity->getVendorItemUnit();
        $snapshot->vendorItemCode = $entity->getVendorItemCode();
        $snapshot->conversionFactor = $entity->getConversionFactor();
        $snapshot->conversionText = $entity->getConversionText();
        $snapshot->vendorUnitPrice = $entity->getVendorUnitPrice();
        $snapshot->pmtTermId = $entity->getPmtTermId();
        $snapshot->deliveryTermId = $entity->getDeliveryTermId();
        $snapshot->leadTime = $entity->getLeadTime();
        $snapshot->taxRate = $entity->getTaxRate();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->currentStatus = $entity->getCurrentStatus();
        $snapshot->targetId = $entity->getTargetId();
        $snapshot->targetClass = $entity->getTargetClass();
        $snapshot->sourceId = $entity->getSourceId();
        $snapshot->sourceClass = $entity->getSourceClass();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->changeBy = $entity->getChangeBy();
        $snapshot->revisionNumber = $entity->getRevisionNumber();
        $snapshot->isPosted = $entity->getIsPosted();
        $snapshot->actualQuantity = $entity->getActualQuantity();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->stockRemarks = $entity->getStockRemarks();
        $snapshot->transactionType = $entity->getTransactionType();
        $snapshot->itemSerialId = $entity->getItemSerialId();
        $snapshot->itemBatchId = $entity->getItemBatchId();
        $snapshot->cogsLocal = $entity->getCogsLocal();
        $snapshot->cogsDoc = $entity->getCogsDoc();
        $snapshot->exchangeRate = $entity->getExchangeRate();
        $snapshot->convertedStandardQuantity = $entity->getConvertedStandardQuantity();
        $snapshot->convertedStandardUnitPrice = $entity->getConvertedStandardUnitPrice();
        $snapshot->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $snapshot->convertedStockUnitPrice = $entity->getConvertedStockUnitPrice();
        $snapshot->convertedPurchaseQuantity = $entity->getConvertedPurchaseQuantity();
        $snapshot->docQuantity = $entity->getDocQuantity();
        $snapshot->docUnitPrice = $entity->getDocUnitPrice();
        $snapshot->docUnit = $entity->getDocUnit();
        $snapshot->isReversed = $entity->getIsReversed();
        $snapshot->reversalDoc = $entity->getReversalDoc();
        $snapshot->reversalReason = $entity->getReversalReason();
        $snapshot->isReversable = $entity->getIsReversable();
        $snapshot->docType = $entity->getDocType();
        $snapshot->localUnitPrice = $entity->getLocalUnitPrice();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->mvUuid = $entity->getMvUuid();
        $snapshot->standardConvertFactor = $entity->getStandardConvertFactor();
        $snapshot->glAccount = $entity->getGlAccount();
        $snapshot->localGrossAmount = $entity->getLocalGrossAmount();
        $snapshot->localNetAmount = $entity->getLocalNetAmount();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->vendorItemName = $entity->getVendorItemName();
        $snapshot->rowIdentifier = $entity->getRowIdentifier();
        $snapshot->discountRate = $entity->getDiscountRate();
        $snapshot->discountAmount = $entity->getDiscountAmount();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->unit = $entity->getUnit();
        $snapshot->grossAmount = $entity->getGrossAmount();
        $snapshot->netAmount = $entity->getNetAmount();
        $snapshot->convertFactorPuchase = $entity->getConvertFactorPuchase();
        $snapshot->convertedPurchaseUnitPrice = $entity->getConvertedPurchaseUnitPrice();
        $snapshot->lastchangeBy = $entity->getLastchangeBy();
        $snapshot->invoiceId = $entity->getInvoiceId();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->trxDate = $entity->getTrxDate();
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->changeOn = $entity->getChangeOn();
         * $snapshot->reversalDate = $entity->getReversalDate();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         */

        if (! $entity->getTrxDate() == null) {
            $snapshot->trxDate = $entity->getTrxDate()->format("Y-m-d");
        }
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getChangeOn() == null) {
            $snapshot->changeOn = $entity->getChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
        }

        if (! $entity->getLastchangeOn() == null) {
            $snapshot->lastchangeOn = $entity->getLastchangeOn()->format("Y-m-d H:i:s");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        // $snapshot->createdBy = $entity->getCreatedBy();
        // $snapshot->lastChangeBy = $entity->getLastChangeBy();
        // $snapshot->item = $entity->getItem();
        // $snapshot->pr = $entity->getPr();
        // $snapshot->po = $entity->getPo();
        // $snapshot->vendorInvoice = $entity->getVendorInvoice();
        // $snapshot->poRow = $entity->getPoRow();
        // $snapshot->grRow = $entity->getGrRow();
        // $snapshot->inventoryGi = $entity->getInventoryGi();
        // $snapshot->inventoryGr = $entity->getInventoryGr();
        // $snapshot->inventoryTransfer = $entity->getInventoryTransfer();
        // $snapshot->wh = $entity->getWh();
        // $snapshot->gr = $entity->getGr();
        // $snapshot->movement = $entity->getMovement();
        // $snapshot->issueFor = $entity->getIssueFor();
        // $snapshot->docCurrency = $entity->getDocCurrency();
        // $snapshot->localCurrency = $entity->getLocalCurrency();
        // $snapshot->project = $entity->getProject();
        // $snapshot->costCenter = $entity->getCostCenter();
        // $snapshot->docUom = $entity->getDocUom();
        // $snapshot->postingPeriod = $entity->getPostingPeriod();
        // $snapshot->whLocation = $entity->getWhLocation();
        // $snapshot->warehouse = $entity->getWarehouse();
        // $snapshot->prRow = $entity->getPrRow();
        // $snapshot->vendor = $entity->getVendor();
        // $snapshot->currency = $entity->getCurrency();
        // $snapshot->pmtMethod = $entity->getPmtMethod();
        // $snapshot->invoiceRow = $entity->getInvoiceRow();

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getItem() !== null) {
            RowMapper::updateItemDetails($snapshot, $entity->getItem());
        }

        if ($entity->getPr() !== null) {
            $snapshot->pr = $entity->getPr()->getId();
        }

        if ($entity->getPo() !== null) {
            $snapshot->po = $entity->getPo()->getId();
        }
        /*
         * if ($entity->getVendorInvoice() !== null) {
         * $snapshot->vendorInvoice = $entity->getVendorInvoice()->getId();
         * $snapshot->invoiceId = $entity->getVendorInvoice()->getSapDoc();
         * }
         */

        if ($entity->getPoRow() !== null) {
            $snapshot->poRow = $entity->getPoRow()->getId();
        }
        if ($entity->getGrRow() !== null) {
            $snapshot->grRow = $entity->getGrRow()->getId();
        }
        if ($entity->getWh() !== null) {
            $snapshot->wh = $entity->getWh()->getId();
        }
        if ($entity->getGr() !== null) {
            $snapshot->gr = $entity->getGr()->getId();
        }
        if ($entity->getIssueFor() !== null) {
            $snapshot->issueFor = $entity->getIssueFor()->getId();
        }
        if ($entity->getMovement() !== null) {
            RowMapper::updateMovementDetails($snapshot, $entity->getMovement());
            $snapshot->transactionType = $entity->getMovement()->getMovementType();
        }
        if ($entity->getDocCurrency() !== null) {
            $snapshot->docCurrency = $entity->getDocCurrency()->getId();
        }
        if ($entity->getLocalCurrency() !== null) {
            $snapshot->localCurrency = $entity->getLocalCurrency()->getId();
        }
        if ($entity->getProject() !== null) {
            $snapshot->project = $entity->getProject()->getId();
        }
        if ($entity->getCostCenter() !== null) {
            $snapshot->costCenter = $entity->getCostCenter()->getId();
        }
        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }
        if ($entity->getPostingPeriod() !== null) {
            $snapshot->postingPeriod = $entity->getPostingPeriod()->getId();
        }
        if ($entity->getWhLocation() != null) {
            $snapshot->whLocation = $entity->getWhLocation()->getId();
        }
        if ($entity->getWh() !== null) {
            RowMapper::updateWarehouseDetails($snapshot, $entity->getWh());
        }
        if ($entity->getPrRow() !== null) {
            RowMapper::updatePRDetails($snapshot, $entity->getPrRow());
        }
        if ($entity->getVendor() !== null) {
            $snapshot->vendor = $entity->getVendor()->getId();
        }
        if ($entity->getCurrency() !== null) {
            $snapshot->currency = $entity->getCurrency()->getId();
        }
        if ($entity->getPmtMethod() !== null) {
            $snapshot->pmtMethod = $entity->getPmtMethod()->getId();
        }

        if ($entity->getInvoiceRow() !== null) {

            if ($entity->getInvoiceRow()->getInvoice() !== null) {
                $snapshot->vendorInvoice = $entity->getInvoiceRow()
                    ->getInvoice()
                    ->getId();
                $snapshot->invoiceId = $entity->getInvoiceRow()
                    ->getInvoice()
                    ->getSapDoc();
            }
        }

        return $snapshot;
    }
}
