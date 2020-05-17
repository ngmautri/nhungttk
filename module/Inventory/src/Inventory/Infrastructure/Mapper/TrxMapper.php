<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\NmtInventoryMv;
use Doctrine\ORM\EntityManager;
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
        $entity->setCreatedBy($snapshot->createdBy);
        $entity->setCompany($snapshot->company);
        $entity->setVendor($snapshot->vendor);
        $entity->setWarehouse($snapshot->warehouse);
        $entity->setPostingPeriod($snapshot->postingPeriod);
        $entity->setCurrency($snapshot->currency);
        $entity->setDocCurrency($snapshot->docCurrency);
        $entity->setLocalCurrency($snapshot->localCurrency);
        $entity->setTargetWarehouse($snapshot->targetWarehouse);
        $entity->setSourceLocation($snapshot->sourceLocation);
        $entity->setTartgetLocation($snapshot->tartgetLocation);

        // =========

        return $entity;
    }
}
