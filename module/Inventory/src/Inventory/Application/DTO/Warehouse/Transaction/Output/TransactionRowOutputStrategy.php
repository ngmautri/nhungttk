<?php
namespace Inventory\Application\DTO\Warehouse\Transaction\Output;

use Application\Entity\NmtInventoryTrx;
use Application\Entity\NmtProcurePrRow;
use Procure\Application\DTO\Pr\PrRowStatusDTO;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDetailDTO;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class TransactionRowOutputStrategy
{

    const OUTPUT_IN_ARRAY = "1";

    const OUTPUT_IN_EXCEL = "2";

    const OUTPUT_IN_HMTL_TABLE = "3";

    const OUTPUT_IN_OPEN_OFFICE = "4";

    protected $output;

    /**
     *
     * @return mixed
     */
    public function getOutput()
    {
        if ($this->output == null)
            $this->output = array();

        return $this->output;
    }

    /**
     *
     * @param mixed $output
     */
    protected function setOutput($output)
    {
        $this->output = $output;
    }

    abstract public function createOutput($result);

    abstract public function createRowOutputFromSnapshot($result);

    /**
     *
     * @param NmtInventoryTrx $entity
     * @return NULL|\Procure\Application\DTO\Pr\PrRowStatusDTO
     */
    public function createDTOFrom(NmtInventoryTrx $entity = null)
    {
        if ($entity == null) {
            return null;
        }

        /**@var \Application\Entity\NmtInventoryTrx $entity ;*/

        $dto = new TransactionRowDetailDTO();

        $dto->id = $entity->getId();
        $dto->token = $entity->getToken();
        $dto->checksum = $entity->getChecksum();
        $dto->trxDate = $entity->getTrxDate();
        $dto->trxTypeId = $entity->getTrxTypeId();
        $dto->flow = $entity->getFlow();
        $dto->quantity = $entity->getQuantity();
        $dto->remarks = $entity->getRemarks();
        $dto->createdOn = $entity->getCreatedOn();
        $dto->isLocked = $entity->getIsLocked();
        $dto->isDraft = $entity->getIsDraft();
        $dto->isActive = $entity->getIsActive();
        $dto->lastChangeOn = $entity->getLastChangeOn();
        $dto->isPreferredVendor = $entity->getIsPreferredVendor();
        $dto->vendorItemUnit = $entity->getVendorItemUnit();
        $dto->vendorItemCode = $entity->getVendorItemCode();
        $dto->conversionFactor = $entity->getConversionFactor();
        $dto->conversionText = $entity->getConversionText();
        $dto->vendorUnitPrice = $entity->getVendorUnitPrice();
        $dto->pmtTermId = $entity->getPmtTermId();
        $dto->deliveryTermId = $entity->getDeliveryTermId();
        $dto->leadTime = $entity->getLeadTime();
        $dto->taxRate = $entity->getTaxRate();
        $dto->currentState = $entity->getCurrentState();
        $dto->currentStatus = $entity->getCurrentStatus();
        $dto->targetId = $entity->getTargetId();
        $dto->targetClass = $entity->getTargetClass();
        $dto->sourceId = $entity->getSourceId();
        $dto->sourceClass = $entity->getSourceClass();
        $dto->docStatus = $entity->getDocStatus();
        $dto->sysNumber = $entity->getSysNumber();
        $dto->changeOn = $entity->getChangeOn();
        $dto->changeBy = $entity->getChangeBy();
        $dto->revisionNumber = $entity->getRevisionNumber();
        $dto->isPosted = $entity->getIsPosted();
        $dto->actualQuantity = $entity->getActualQuantity();
        $dto->transactionStatus = $entity->getTransactionStatus();
        $dto->stockRemarks = $entity->getStockRemarks();
        $dto->transactionType = $entity->getTransactionType();
        $dto->itemSerialId = $entity->getItemSerialId();
        $dto->itemBatchId = $entity->getItemBatchId();
        $dto->cogsLocal = $entity->getCogsLocal();
        $dto->cogsDoc = $entity->getCogsDoc();
        $dto->exchangeRate = $entity->getExchangeRate();
        $dto->convertedStandardQuantity = $entity->getConvertedStandardQuantity();
        $dto->convertedStandardUnitPrice = $entity->getConvertedStandardUnitPrice();
        $dto->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $dto->convertedStockUnitPrice = $entity->getConvertedStockUnitPrice();
        $dto->convertedPurchaseQuantity = $entity->getConvertedPurchaseQuantity();
        $dto->docQuantity = $entity->getDocQuantity();
        $dto->docUnitPrice = $entity->getDocUnitPrice();
        $dto->docUnit = $entity->getDocUnit();
        $dto->isReversed = $entity->getIsReversed();
        $dto->reversalDate = $entity->getReversalDate();
        $dto->reversalDoc = $entity->getReversalDoc();
        $dto->reversalReason = $entity->getReversalReason();
        $dto->isReversable = $entity->getIsReversable();
        $dto->docType = $entity->getDocType();
        $dto->localUnitPrice = $entity->getLocalUnitPrice();
        $dto->reversalBlocked = $entity->getReversalBlocked();
        $dto->mvUuid = $entity->getMvUuid();

        if ($entity->getCreatedBy() !== null)
            $dto->createdBy = $entity->getCreatedBy()->getId();

        if ($entity->getLastChangeBy() !== null)
            $dto->lastChangeBy = $entity->getLastChangeBy()->getId();

        if ($entity->getItem() !== null)
            $dto->item = $entity->getItem()->getId();

        if ($entity->getPr() !== null)
            $dto->pr = $entity->getPr()->getId();

        if ($entity->getPo() !== null)
            $dto->po = $entity->getPo()->getId();

        if ($entity->getVendorInvoice() !== null)
            $dto->vendorInvoice = $entity->getVendorInvoice()->getId();

        if ($entity->getPoRow() !== null)
            $dto->poRow = $entity->getPoRow()->getId();

        if ($entity->getGrRow() !== null)
            $dto->grRow = $entity->getGrRow()->getId();

        if ($entity->getWh() !== null)
            $dto->wh = $entity->getWh()->getId();

        if ($entity->getGr() !== null)
            $dto->gr = $entity->getGr()->getId();

        if ($entity->getMovement() !== null)
            $dto->movement = $entity->getMovement()->getId();

        if ($entity->getIssueFor() !== null)
            $dto->issueFor = $entity->getIssueFor()->getId();

        if ($entity->getDocCurrency() !== null)
            $dto->docCurrency = $entity->getDocCurrency()->getId();

        if ($entity->getLocalCurrency() !== null)
            $dto->localCurrency = $entity->getLocalCurrency()->getId();

        if ($entity->getProject() !== null)
            $dto->project = $entity->getProject()->getId();

        if ($entity->getCostCenter() !== null)
            $dto->costCenter = $entity->getCostCenter()->getId();

        if ($entity->getDocUom() !== null)
            $dto->docUom = $entity->getDocUom()->getId();

        if ($entity->getPostingPeriod() !== null)
            $dto->postingPeriod = $entity->getPostingPeriod()->getId();

        if ($entity->getWhLocation() !== null)
            $dto->whLocation = $entity->getWhLocation()->getId();

        if ($entity->getPrRow() !== null)
            $dto->prRow = $entity->getPrRow()->getId();

        if ($entity->getVendor() !== null)
            $dto->vendor = $entity->getVendor()->getId();

        if ($entity->getCurrency() !== null)
            $dto->currency = $entity->getCurrency()->getId();

        if ($entity->getPmtMethod() !== null)
            $dto->pmtMethod = $entity->getPmtMethod()->getId();

        if ($entity->getInvoiceRow() !== null)
            $dto->invoiceRow = $entity->getInvoiceRow()->getId();

        return $dto;
    }
}
