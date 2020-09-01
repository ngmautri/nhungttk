<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\NmtProcurePr;
use Application\Entity\NmtProcurePrRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Procure\Domain\PurchaseRequest\PRSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrMapper
{

    public static function mapSnapshotEntity(EntityManager $doctrineEM, PRSnapshot $snapshot, NmtProcurePr $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        // $entity->setId($snapshot->id);
        $entity->setPrAutoNumber($snapshot->prAutoNumber);
        $entity->setPrNumber($snapshot->prNumber);
        $entity->setPrName($snapshot->prName);
        $entity->setKeywords($snapshot->keywords);
        $entity->setRemarks($snapshot->remarks);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setIsActive($snapshot->isActive);
        $entity->setStatus($snapshot->status);
        $entity->setToken($snapshot->token);
        $entity->setChecksum($snapshot->checksum);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setTotalRowManual($snapshot->totalRowManual);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setDocType($snapshot->docType);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setUuid($snapshot->uuid);
        $entity->setDocNumber($snapshot->docNumber);

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         * $entity->setDocDate($snapshot->docDate);
         * $entity->setSubmittedOn($snapshot->submittedOn);
         */

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        if ($snapshot->docDate !== null) {
            $entity->setDocDate(new \DateTime($snapshot->docDate));
        }

        if ($snapshot->submittedOn !== null) {
            $entity->setSubmittedOn(new \DateTime($snapshot->submittedOn));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setDepartment($snapshot->department);
         * $entity->setCompany($snapshot->company);
         * $entity->setWarehouse($snapshot->warehouse);
         */
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

        if ($snapshot->department > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationDepartment $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationDepartment')->find($snapshot->department);
            $entity->setDepartment($obj);
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

        return $entity;
    }

    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, PRRowSnapshot $snapshot, NmtProcurePrRow $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        // $entity->setId($snapshot->id);
        $entity->setRowNumber($snapshot->rowNumber);
        $entity->setRowIdentifer($snapshot->rowIdentifer);
        $entity->setToken($snapshot->token);
        $entity->setChecksum($snapshot->checksum);
        $entity->setPriority($snapshot->priority);
        $entity->setRowName($snapshot->rowName);
        $entity->setRowDescription($snapshot->rowDescription);
        $entity->setRowCode($snapshot->rowCode);
        $entity->setRowUnit($snapshot->rowUnit);
        $entity->setConversionFactor($snapshot->conversionFactor);
        $entity->setConversionText($snapshot->conversionText);
        $entity->setQuantity($snapshot->quantity);
        $entity->setIsDraft($snapshot->isDraft);
        $entity->setIsActive($snapshot->isActive);
        $entity->setRemarks($snapshot->remarks);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setFaRemarks($snapshot->faRemarks);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setDocStatus($snapshot->docStatus);
        $entity->setWorkflowStatus($snapshot->workflowStatus);
        $entity->setTransactionStatus($snapshot->transactionStatus);
        $entity->setConvertedStockQuantity($snapshot->convertedStockQuantity);
        $entity->setConvertedStandardQuantiy($snapshot->convertedStandardQuantiy);
        $entity->setDocQuantity($snapshot->docQuantity);
        $entity->setDocUnit($snapshot->docUnit);
        $entity->setDocType($snapshot->docType);
        $entity->setReversalBlocked($snapshot->reversalBlocked);
        $entity->setDocVersion($snapshot->docVersion);
        $entity->setUuid($snapshot->uuid);
        $entity->setStandardConvertFactor($snapshot->standardConvertFactor);
        $entity->setVendorItemName($snapshot->vendorItemName);
        $entity->setVendorItemCode($snapshot->vendorItemCode);

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         * $entity->setEdt($snapshot->edt);
         */
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        if ($snapshot->edt !== null) {
            $entity->setEdt(new \DateTime($snapshot->edt));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         * $entity->setPr($snapshot->pr);
         * $entity->setItem($snapshot->item);
         * $entity->setProject($snapshot->project);
         * $entity->setDocUom($snapshot->docUom);
         * $entity->setWarehouse($snapshot->warehouse);
         */
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

        if ($snapshot->pr > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePr $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePr')->find($snapshot->pr);
            $entity->setPr($obj);
        }

        if ($snapshot->item > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->item);
            $entity->setItem($obj);
        }

        if ($snapshot->project > 0) {
            /**
             *
             * @var \Application\Entity\NmtPmProject $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtPmProject')->find($snapshot->project);
            $entity->setProject($obj);
        }

        if ($snapshot->docUom > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationUom $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->find($snapshot->docUom);
            $entity->setDocUom($obj);
        }

        if ($snapshot->warehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->warehouse);
            $entity->setWarehouse($obj);
        }

        return $entity;
    }

    public static function createSnapshot(EntityManager $doctrineEM, NmtProcurePr $entity, $snapshot = null)
    {
        if ($entity == null || $doctrineEM == null) {
            return null;
        }

        if (! $snapshot instanceof PRSnapshot) {
            $snapshot = new PRSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $snapshot->id = $entity->getId();
        $snapshot->prAutoNumber = $entity->getPrAutoNumber();
        $snapshot->prNumber = $entity->getPrNumber();
        $snapshot->prName = $entity->getPrName();
        $snapshot->keywords = $entity->getKeywords();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->status = $entity->getStatus();
        $snapshot->token = $entity->getToken();
        $snapshot->checksum = $entity->getChecksum();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->totalRowManual = $entity->getTotalRowManual();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->docType = $entity->getDocType();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->docNumber = $entity->getDocNumber();

        // ============================
        // Addtional Mapping
        // ============================
        $snapshot->sysNumber = $snapshot->getPrAutoNumber();
        $snapshot->docNumber = $snapshot->getPrName();
        $snapshot->docDate = $snapshot->getSubmittedOn();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->submittedOn = $entity->getSubmittedOn();
         * $snapshot->docDate = $entity->getDocDate();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastchangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getSubmittedOn() == null) {
            $snapshot->submittedOn = $entity->getSubmittedOn()->format("Y-m-d");
        }

        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->department = $entity->getDepartment();
         * $snapshot->company = $entity->getCompany();
         * $snapshot->warehouse = $entity->getWarehouse();
         */

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = $entity->getCreatedBy()->getFirstname() . " " . $entity->getCreatedBy()->getLastname();
        }

        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangedByName = $entity->getLastchangeBy()->getFirstname() . " " . $entity->getLastchangeBy()->getLastname();
        }

        if ($entity->getDepartment() !== null) {
            $snapshot->department = $entity->getDepartment()->getNodeId();
        }

        if ($entity->getCompany() !== null) {
            HeaderMapper::updateCompanyDetails($snapshot, $entity->getCompany());
        }

        if ($entity->getWarehouse() !== null) {
            HeaderMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        return $snapshot;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param NmtProcurePrRow $entity
     * @return \Procure\Domain\PurchaseRequest\PRRowSnapshot
     */
    public static function createRowSnapshot(EntityManager $doctrineEM, NmtProcurePrRow $entity)
    {
        if ($entity == null || $doctrineEM == null) {
            return null;
        }
        $snapshot = new PRRowSnapshot();
        // =================================
        // Mapping None-Object Field
        // =================================
        $snapshot->id = $entity->getId();
        $snapshot->rowNumber = $entity->getRowNumber();
        $snapshot->rowIdentifer = $entity->getRowIdentifer();
        $snapshot->token = $entity->getToken();
        $snapshot->checksum = $entity->getChecksum();
        $snapshot->priority = $entity->getPriority();
        $snapshot->rowName = $entity->getRowName();
        $snapshot->rowDescription = $entity->getRowDescription();
        $snapshot->rowCode = $entity->getRowCode();
        $snapshot->rowUnit = $entity->getRowUnit();
        $snapshot->conversionFactor = $entity->getConversionFactor();
        $snapshot->conversionText = $entity->getConversionText();
        $snapshot->quantity = $entity->getQuantity();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->faRemarks = $entity->getFaRemarks();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $snapshot->convertedStandardQuantiy = $entity->getConvertedStandardQuantiy();
        $snapshot->docQuantity = $entity->getDocQuantity();
        $snapshot->docUnit = $entity->getDocUnit();
        $snapshot->docType = $entity->getDocType();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->standardConvertFactor = $entity->getStandardConvertFactor();
        $snapshot->vendorItemName = $entity->getVendorItemName();
        $snapshot->vendorItemCode = $entity->getVendorItemCode();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->edt = $entity->getEdt();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getEdt() == null) {
            $snapshot->edt = $entity->getEdt()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $snapshot->pr = $entity->getPr();
         *
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->item = $entity->getItem();
         * $snapshot->project = $entity->getProject();
         * $snapshot->docUom = $entity->getDocUom();
         * $snapshot->warehouse = $entity->getWarehouse();
         */

        // Parent ID.
        if ($entity->getPr() !== null) {
            RowMapper::updatePRHeaderDetails($snapshot, $entity->getPr());
        }

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = sprintf("%s %s", $entity->getCreatedBy()->getFirstname(), $entity->getCreatedBy()->getFirstname());
        }

        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangeByName = sprintf("%s %s", $entity->getLastchangeBy()->getFirstname(), $entity->getLastchangeBy()->getFirstname());
        }

        if ($entity->getItem() !== null) {
            RowMapper::updateItemDetails($snapshot, $entity->getItem());
        }

        if ($entity->getProject() !== null) {
            $snapshot->project = $entity->getProject()->getId();
        }

        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }

        if ($entity->getWarehouse() !== null) {
            RowMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        return $snapshot;
    }

    /**
     *
     * @param NmtProcurePrRow $entity
     * @return \Procure\Domain\PurchaseRequest\PRRowSnapshot
     */
    public static function convertToRowSnapshot(NmtProcurePrRow $entity)
    {
        if ($entity == null) {
            return null;
        }
        $snapshot = new PRRowSnapshot();
        // =================================
        // Mapping None-Object Field
        // =================================
        $snapshot->id = $entity->getId();
        $snapshot->rowNumber = $entity->getRowNumber();
        $snapshot->rowIdentifer = $entity->getRowIdentifer();
        $snapshot->token = $entity->getToken();
        $snapshot->checksum = $entity->getChecksum();
        $snapshot->priority = $entity->getPriority();
        $snapshot->rowName = $entity->getRowName();
        $snapshot->rowDescription = $entity->getRowDescription();
        $snapshot->rowCode = $entity->getRowCode();
        $snapshot->rowUnit = $entity->getRowUnit();
        $snapshot->conversionFactor = $entity->getConversionFactor();
        $snapshot->conversionText = $entity->getConversionText();
        $snapshot->quantity = $entity->getQuantity();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->faRemarks = $entity->getFaRemarks();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $snapshot->convertedStandardQuantiy = $entity->getConvertedStandardQuantiy();
        $snapshot->docQuantity = $entity->getDocQuantity();
        $snapshot->docUnit = $entity->getDocUnit();
        $snapshot->docType = $entity->getDocType();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->standardConvertFactor = $entity->getStandardConvertFactor();
        $snapshot->vendorItemName = $entity->getVendorItemName();
        $snapshot->vendorItemCode = $entity->getVendorItemCode();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->edt = $entity->getEdt();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getEdt() == null) {
            $snapshot->edt = $entity->getEdt()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $snapshot->pr = $entity->getPr();
         *
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->item = $entity->getItem();
         * $snapshot->project = $entity->getProject();
         * $snapshot->docUom = $entity->getDocUom();
         * $snapshot->warehouse = $entity->getWarehouse();
         */

        // Parent ID.
        if ($entity->getPr() !== null) {
            RowMapper::updatePRHeaderDetails($snapshot, $entity->getPr());
        }

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = sprintf("%s %s", $entity->getCreatedBy()->getFirstname(), $entity->getCreatedBy()->getFirstname());
        }

        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangeByName = sprintf("%s %s", $entity->getLastchangeBy()->getFirstname(), $entity->getLastchangeBy()->getFirstname());
        }

        if ($entity->getItem() !== null) {
            RowMapper::updateItemDetails($snapshot, $entity->getItem());
        }

        if ($entity->getProject() !== null) {
            $snapshot->project = $entity->getProject()->getId();
        }

        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }

        if ($entity->getWarehouse() !== null) {
            RowMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        return $snapshot;
    }

    /**
     *
     * @param NmtProcurePrRow $entity
     * @param PRRowSnapshot $snapshot
     * @return \Procure\Domain\PurchaseRequest\PRRowSnapshot
     */
    private function _convertRow(NmtProcurePrRow $entity, PRRowSnapshot $snapshot)
    {

        // =================================
        // Mapping None-Object Field
        // =================================
        $snapshot->id = $entity->getId();
        $snapshot->rowNumber = $entity->getRowNumber();
        $snapshot->rowIdentifer = $entity->getRowIdentifer();
        $snapshot->token = $entity->getToken();
        $snapshot->checksum = $entity->getChecksum();
        $snapshot->priority = $entity->getPriority();
        $snapshot->rowName = $entity->getRowName();
        $snapshot->rowDescription = $entity->getRowDescription();
        $snapshot->rowCode = $entity->getRowCode();
        $snapshot->rowUnit = $entity->getRowUnit();
        $snapshot->conversionFactor = $entity->getConversionFactor();
        $snapshot->conversionText = $entity->getConversionText();
        $snapshot->quantity = $entity->getQuantity();
        $snapshot->isDraft = $entity->getIsDraft();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->faRemarks = $entity->getFaRemarks();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->convertedStockQuantity = $entity->getConvertedStockQuantity();
        $snapshot->convertedStandardQuantiy = $entity->getConvertedStandardQuantiy();
        $snapshot->docQuantity = $entity->getDocQuantity();
        $snapshot->docUnit = $entity->getDocUnit();
        $snapshot->docType = $entity->getDocType();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->docVersion = $entity->getDocVersion();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->standardConvertFactor = $entity->getStandardConvertFactor();
        $snapshot->vendorItemName = $entity->getVendorItemName();
        $snapshot->vendorItemCode = $entity->getVendorItemCode();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastchangeOn = $entity->getLastchangeOn();
         * $snapshot->edt = $entity->getEdt();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getEdt() == null) {
            $snapshot->edt = $entity->getEdt()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $snapshot->pr = $entity->getPr();
         *
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastchangeBy = $entity->getLastchangeBy();
         * $snapshot->item = $entity->getItem();
         * $snapshot->project = $entity->getProject();
         * $snapshot->docUom = $entity->getDocUom();
         * $snapshot->warehouse = $entity->getWarehouse();
         */

        // Parent ID.
        if ($entity->getPr() !== null) {
            RowMapper::updatePRHeaderDetails($snapshot, $entity->getPr());
        }

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
            $snapshot->createdByName = sprintf("%s %s", $entity->getCreatedBy()->getFirstname(), $entity->getCreatedBy()->getFirstname());
        }

        if ($entity->getLastchangeBy() !== null) {
            $snapshot->lastchangeBy = $entity->getLastchangeBy()->getId();
            $snapshot->lastChangeByName = sprintf("%s %s", $entity->getLastchangeBy()->getFirstname(), $entity->getLastchangeBy()->getFirstname());
        }

        if ($entity->getItem() !== null) {
            RowMapper::updateItemDetails($snapshot, $entity->getItem());
        }

        if ($entity->getProject() !== null) {
            $snapshot->project = $entity->getProject()->getId();
        }

        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }

        if ($entity->getWarehouse() !== null) {
            RowMapper::updateWarehouseDetails($snapshot, $entity->getWarehouse());
        }

        return $snapshot;
    }
}
