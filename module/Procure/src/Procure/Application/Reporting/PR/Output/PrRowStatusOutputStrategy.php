<?php
namespace Procure\Application\Reporting\PR\Output;

use Application\Entity\NmtProcurePrRow;
use Procure\Application\DTO\Pr\PrRowStatusDTO;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class PrRowStatusOutputStrategy
{
    const OUTPUT_IN_ARRAY = "1";
    const OUTPUT_IN_EXCEL = "2";
    const OUTPUT_IN_HMTL_TABLE = "3";
    
    
    abstract public function createOutput($result);
    
    /**
     *
     * @param NmtProcurePrRow $entity
     * @return NULL|\Procure\Application\DTO\Pr\PrRowStatusDTO
     */
    public function createDTOFrom(NmtProcurePrRow $entity = null)
    {
        if ($entity == null) {
            return null;
        }

        /**@var \Application\Entity\NmtProcurePrRow $entity ;*/

        $dto = new PrRowStatusDTO();

        $dto->id = $entity->getId();
        $dto->rowNumber = $entity->getRowNumber();
        $dto->rowIdentifer = $entity->getRowIdentifer();
        $dto->token = $entity->getToken();
        $dto->checksum = $entity->getChecksum();
        $dto->priority = $entity->getPriority();
        $dto->rowName = $entity->getRowName();
        $dto->rowDescription = $entity->getRowDescription();
        $dto->rowCode = $entity->getRowCode();
        $dto->rowUnit = $entity->getRowUnit();
        $dto->conversionFactor = $entity->getConversionFactor();
        $dto->conversionText = $entity->getConversionText();
        $dto->quantity = $entity->getQuantity();
        $dto->edt = $entity->getEdt();
        $dto->isDraft = $entity->getIsDraft();
        $dto->isActive = $entity->getIsActive();
        $dto->createdOn = $entity->getCreatedOn();
        $dto->remarks = $entity->getRemarks();
        $dto->lastChangeOn = $entity->getLastChangeOn();
        $dto->currentState = $entity->getCurrentState();
        $dto->faRemarks = $entity->getFaRemarks();
        $dto->revisionNo = $entity->getRevisionNo();
        $dto->docStatus = $entity->getDocStatus();
        $dto->workflowStatus = $entity->getWorkflowStatus();
        $dto->transactionStatus = $entity->getTransactionStatus();
        $dto->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $dto->convertedStandardQuantiy = $entity->getConvertedStandardQuantiy();
        $dto->docQuantity = $entity->getDocQuantity();
        $dto->docUnit = $entity->getDocUnit();
        $dto->docType = $entity->getDocType();
        $dto->reversalBlocked = $entity->getReversalBlocked();

        if ($entity->getCreatedBy() !== null) {
            $dto->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getPr() !== null) {
            $dto->pr = $entity->getPr()->getId();
        }

        if ($entity->getItem() !== null) {
            $dto->item = $entity->getItem()->getId();
        }

        if ($entity->getProject() !== null) {
            $dto->project = $entity->getProject()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $dto->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getDocUom() !== null) {
            $dto->docUom = $entity->getDocUom()->getId();
        }

        if ($entity->getWarehouse() !== null) {
            $dto->warehouse = $entity->getWarehouse()->getId();
        }

        return $dto;
    }

  
}
