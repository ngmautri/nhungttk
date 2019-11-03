<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\APInvoice\APDocRowSnapshot;
use Procure\Domain\APInvoice\APDocSnapshot;
use Procure\Domain\APInvoice\APDocDetailsSnapshot;
use Procure\Domain\APInvoice\APDocRowDetailsSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param APDocSnapshot $snapshot
     * @param FinVendorInvoice $entity
     * @return NULL|\Application\Entity\FinVendorInvoice
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, APDocSnapshot $snapshot, FinVendorInvoice $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param APDocRowSnapshot $snapshot
     * @param FinVendorInvoiceRow $entity
     * @return NULL|\Application\Entity\FinVendorInvoiceRow
     */
    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, APDocRowSnapshot $snapshot, FinVendorInvoiceRow $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        return $entity;
    }

    /**
     *
     * @param FinVendorInvoice $entity
     * @return NULL|\Procure\Domain\APInvoice\APDocSnapshot
     */
    public static function createDetailSnapshot(FinVendorInvoice $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new APDocSnapshot();

        return $snapshot;
    }

   /**
    * 
    * @param FinVendorInvoiceRow $entity
    * @return NULL|\Procure\Domain\APInvoice\APDocRowDetailsSnapshot
    */
    public static function createRowDetailSnapshot(FinVendorInvoiceRow $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new APDocRowDetailsSnapshot();
        
        // Mapping Reference
        // =====================
        
        //$snapshot->invoice= $entity->getInvoice();
        if ($entity->getInvoice() !== null) {
            $snapshot->invoice = $entity->getInvoice()->getId();
        }
        
        
        //$snapshot->glAccount= $entity->getGlAccount();
        if ($entity->getGlAccount() !== null) {
            $snapshot->glAccount = $entity->getGlAccount()->getId();
        }
        
        
        //$snapshot->costCenter= $entity->getCostCenter();
        if ($entity->getCostCenter() !== null) {
            $snapshot->costCenter = $entity->getCostCenter()->getId();
        }
        
        
        //$snapshot->docUom= $entity->getDocUom();
        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }
        
        //$snapshot->prRow= $entity->getPrRow();
        if ($entity->getPrRow() !== null) {
            $snapshot->prRow = $entity->getPrRow()->getId();
        }
        
        //$snapshot->createdBy= $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }
        
        //$snapshot->warehouse= $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
        }
        
        //$snapshot->lastchangeBy= $entity->getLastchangeBy();
        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
        }
        
        
        //$snapshot->poRow= $entity->getPoRow();
        if ($entity->getPoRow() !== null) {
            $snapshot->poRow = $entity->getPoRow()->getId();
        }
        
        //$snapshot->item= $entity->getItem();
        if ($entity->getItem() !== null) {
            $snapshot->item = $entity->getItem()->getId();
        }
        
        //$snapshot->grRow= $entity->getGrRow();
        if ($entity->getGrRow() !== null) {
            $snapshot->grRow = $entity->getGrRow()->getId();
        }
        
        
        // Mapping Date
        // =====================
          
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }
        
        if (! $entity->getLastchangeOn() == null) {
            $snapshot->lastchangeOn = $entity->getLastchangeOn()->format("Y-m-d");
        }
        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
        }
        
        // Mapping None-Object Field
        // =====================
        $snapshot->id= $entity->getId();
        $snapshot->rowNumber= $entity->getRowNumber();
        $snapshot->token= $entity->getToken();
        $snapshot->quantity= $entity->getQuantity();
        $snapshot->unitPrice= $entity->getUnitPrice();
        $snapshot->netAmount= $entity->getNetAmount();
        $snapshot->unit= $entity->getUnit();
        $snapshot->itemUnit= $entity->getItemUnit();
        $snapshot->conversionFactor= $entity->getConversionFactor();
        $snapshot->converstionText= $entity->getConverstionText();
        $snapshot->taxRate= $entity->getTaxRate();
        $snapshot->remarks= $entity->getRemarks();
        $snapshot->isActive= $entity->getIsActive();
      
        $snapshot->currentState= $entity->getCurrentState();
        $snapshot->vendorItemCode= $entity->getVendorItemCode();
        $snapshot->traceStock= $entity->getTraceStock();
        $snapshot->grossAmount= $entity->getGrossAmount();
        $snapshot->taxAmount= $entity->getTaxAmount();
        $snapshot->faRemarks= $entity->getFaRemarks();
        $snapshot->rowIdentifer= $entity->getRowIdentifer();
        $snapshot->discountRate= $entity->getDiscountRate();
        $snapshot->revisionNo= $entity->getRevisionNo();
        $snapshot->localUnitPrice= $entity->getLocalUnitPrice();
        $snapshot->docUnitPrice= $entity->getDocUnitPrice();
        $snapshot->exwUnitPrice= $entity->getExwUnitPrice();
        $snapshot->exwCurrency= $entity->getExwCurrency();
        $snapshot->localNetAmount= $entity->getLocalNetAmount();
        $snapshot->localGrossAmount= $entity->getLocalGrossAmount();
        $snapshot->docStatus= $entity->getDocStatus();
        $snapshot->workflowStatus= $entity->getWorkflowStatus();
        $snapshot->transactionType= $entity->getTransactionType();
        $snapshot->isDraft= $entity->getIsDraft();
        $snapshot->isPosted= $entity->getIsPosted();
        $snapshot->transactionStatus= $entity->getTransactionStatus();
        $snapshot->totalExwPrice= $entity->getTotalExwPrice();
        $snapshot->convertFactorPurchase= $entity->getConvertFactorPurchase();
        $snapshot->convertedPurchaseQuantity= $entity->getConvertedPurchaseQuantity();
        $snapshot->convertedStockQuantity= $entity->getConvertedStockQuantity();
        $snapshot->convertedStockUnitPrice= $entity->getConvertedStockUnitPrice();
        $snapshot->convertedStandardQuantity= $entity->getConvertedStandardQuantity();
        $snapshot->convertedStandardUnitPrice= $entity->getConvertedStandardUnitPrice();
        $snapshot->docQuantity= $entity->getDocQuantity();
        $snapshot->docUnit= $entity->getDocUnit();
        $snapshot->convertedPurchaseUnitPrice= $entity->getConvertedPurchaseUnitPrice();
        $snapshot->isReversed= $entity->getIsReversed();
        $snapshot->reversalReason= $entity->getReversalReason();
        $snapshot->reversalDoc= $entity->getReversalDoc();
        $snapshot->isReversable= $entity->getIsReversable();
        $snapshot->docType= $entity->getDocType();
        $snapshot->descriptionText= $entity->getDescriptionText();
        $snapshot->vendorItemName= $entity->getVendorItemName();
        $snapshot->reversalBlocked= $entity->getReversalBlocked();
   
        return $snapshot;
    }
}
