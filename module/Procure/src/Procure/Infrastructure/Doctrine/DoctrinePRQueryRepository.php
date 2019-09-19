<?php
namespace Procure\Infrastructure\Doctrine;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Application\Entity\NmtProcurePr;
use Application\Entity\NmtProcurePrRow;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\PurchaseRequest\PRQueryRepositoryInterface;
use Procure\Domain\PurchaseRequest\PRRowDetailsSnapshot;
use Procure\Domain\PurchaseRequest\PRDetailsSnapshot;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Domain\PurchaseRequest\GenericPR;
use Procure\Infrastructure\Doctrine\SQL\PrSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePRQueryRepository  extends AbstractDoctrineRepository implements PRQueryRepositoryInterface
{
    /**
     *
     * {@inheritDoc}
     * @see \Procure\Domain\PurchaseRequest\PRQueryRepositoryInterface::getPriceOfItem()
     */
    public function getPriceOfItem($id, $token = null)
    {}
    
    

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\PRQueryRepositoryInterface::getPRDetailsById()
     */
    public function getPRDetailsById($id, $token = null)
    {
        $rows = $this->getPrRowsDetails($id);

        if (count($rows) == 0) {
            return;
        }

        /**
         *
         * @var \Application\Entity\NmtProcurePr $pr ;
         */
        $pr = null;
        $completed = True;
        $docRowsArray = array();
        $totalRows = 0;
        $totalActiveRows = 0;
        $completedRows = 0;

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcurePrRow $rowEntity ;*/
            $rowEntity = $r[0];

            if ($pr == null) {
                $pr = $rowEntity->getPR();
            }

            $rowDetailSnapshot = $this->createRowDetailsSnapshot($rowEntity);

            if ($rowDetailSnapshot == null) {
                continue;
            }

            $totalRows ++;
            $totalActiveRows ++;

            $rowDetailSnapshot->draftPOQuantity = $r['po_qty'];
            $rowDetailSnapshot->postedPOQuantity = $r['posted_po_qty'];
            $rowDetailSnapshot->draftGrQuantity = $r['gr_qty'];
            $rowDetailSnapshot->postedGrQuantity = $r['posted_gr_qty'];
            $rowDetailSnapshot->draftStockGRQuantity = $r['stock_gr_qty'];
            $rowDetailSnapshot->postedStockGRQuantity = $r['posted_stock_gr_qty'];
            $rowDetailSnapshot->draftAPQuantity = $r['ap_qty'];
            $rowDetailSnapshot->postedAPQuantity = $r['posted_ap_qty'];
            $rowDetailSnapshot->prName = $r['pr_name'];
            $rowDetailSnapshot->prYear = $r['pr_year'];
            $rowDetailSnapshot->itemName = $r['item_name'];
            $rowDetailSnapshot->lastVendorName = $r['vendor_name'];
            $rowDetailSnapshot->lastUnitPrice = $r['unit_price'];
            $rowDetailSnapshot->lastCurrency = $r['currency_iso3'];

            $docRow = new PRRow();
            $docRow->makeFromDetailsSnapshot($rowDetailSnapshot);
            $docRowsArray[] = $docRow;
        }

        $detailsSnapshot = $this->createPRDetailSnapshot($pr);
        if ($detailsSnapshot == null) {
            return null;
        }
        $rootEntity = new GenericPR();
        $rootEntity->makeFromDetailsSnapshot($detailsSnapshot);

        $rootEntity->setDocRows($docRowsArray);
        return $rootEntity;
    }

    public function getHeaderById($id, $token = null)
    {}

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}

    /**
     *
     * @param int $id
     * @return array|NULL
     */
    private function getPrRowsDetails($prId, $sort_by = null, $sort = "ASC")
    {
        $sql1 = PrSQL::PR_ROW_SQL_1;

        $sql_tmp1 = ' AND nmt_procure_pr_row.pr_id=' . $prId;

        switch ($sort_by) {
            case "itemName":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_inventory_item.item_name " . $sort;
                break;

            case "prNumber":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_procure_pr.pr_number " . $sort;
                break;

            case "balance":
                $sql_tmp1 = $sql_tmp1 . " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.posted_gr_qty,0) " . $sort;
                break;
        }

        $sql = sprintf($sql1, $prId, $prId, $prId, $prId, $sql_tmp1);

        $sql = $sql . ";";

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');

            $rsm->addScalarResult("pr_qty", "pr_qty");

            $rsm->addScalarResult("po_qty", "po_qty");
            $rsm->addScalarResult("posted_po_qty", "posted_po_qty");

            $rsm->addScalarResult("gr_qty", "gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");

            $rsm->addScalarResult("stock_gr_qty", "stock_gr_qty");
            $rsm->addScalarResult("posted_stock_gr_qty", "posted_stock_gr_qty");

            $rsm->addScalarResult("ap_qty", "ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");

            $rsm->addScalarResult("pr_name", "pr_name");
            $rsm->addScalarResult("pr_year", "pr_year");

            $rsm->addScalarResult("item_name", "item_name");
            $rsm->addScalarResult("vendor_name", "vendor_name");
            $rsm->addScalarResult("unit_price", "unit_price");
            $rsm->addScalarResult("currency_iso3", "currency_iso3");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param NmtProcurePr $entity
     * @return NULL|\Procure\Domain\PurchaseRequest\PRDetailsSnapshot
     */
    private function createPRDetailSnapshot(NmtProcurePr $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new PRDetailsSnapshot();

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
        $snapshot->submittedOn = $entity->getSubmittedOn();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->totalRowManual = $entity->getTotalRowManual();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docStatus = $entity->getDocStatus();
        $snapshot->workflowStatus = $entity->getWorkflowStatus();
        $snapshot->transactionStatus = $entity->getTransactionStatus();
        $snapshot->docType = $entity->getDocType();
        $snapshot->reversalBlocked = $entity->getReversalBlocked();
        $snapshot->uuid = $entity->getUuid();

        // Mapping Date
        // =====================

        $snapshot->createdOn = $entity->getCreatedOn();
        $snapshot->lastchangeOn = $entity->getLastchangeOn();

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // Mapping Reference
        // =====================

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

        $snapshot->department = $entity->getDepartment();
        if ($entity->getDepartment() !== null) {
            $snapshot->department = $entity->getDepartment()->getId();
        }

        $snapshot->company = $entity->getCompany();
        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        // $snapshot->warehouse = $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
            $snapshot->warehouseName = $entity->getWarehouse()->getWhName();
            $snapshot->warehouseCode = $entity->getWarehouse()->getWhCode();
        }

        return $snapshot;
    }

    /**
     *
     * @param NmtProcurePrRow $entity
     * @return NULL|\Procure\Domain\PurchaseRequest\PRRowDetailsSnapshot
     */
    private function createRowDetailsSnapshot(NmtProcurePrRow $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new PRRowDetailsSnapshot();

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
        $snapshot->edt = $entity->getEdt();
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

        // Mapping Date
        // =====================

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // Mapping Reference
        // =====================
        // $snapshot->createdBy= $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        // $snapshot->pr = $entity->getPr();
        if ($entity->getPr() !== null) {
            $snapshot->pr = $entity->getPr()->getId();
        }

        // $snapshot->item = $entity->getItem();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->item = $entity->getItem()->getId();

            $snapshot->itemToken = $entity->getItem()->getToken();
            $snapshot->itemCheckSum = $entity->getItem()->getChecksum();

            $snapshot->itemName = $entity->getItem()->getItemName();
            $snapshot->itemName1 = $entity->getItem()->getItemNameForeign();

            $snapshot->itemSKU = $entity->getItem()->getItemSku();

            $snapshot->itemSKU1 = $entity->getItem()->getItemSku1();
            $snapshot->itemSKU2 = $entity->getItem()->getItemSku2();

            $snapshot->itemVersion = $entity->getItem()->getRevisionNo();

            if ($entity->getItem()->getStandardUom() != null) {
                $snapshot->itemStandardUnit = $entity->getItem()
                    ->getStandardUom()
                    ->getId();
                $snapshot->itemStandardUnitName = $entity->getItem()
                    ->getStandardUom()
                    ->getUomCode();
            }
        }

        // $snapshot->project = $entity->getProject();
        if ($entity->getProject() !== null) {
            $snapshot->project = $entity->getProject()->getId();
        }

        // $snapshot->lastChangeBy = $entity->getLastChangeBy();
        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        $snapshot->docUom = $entity->getDocUom();
        if ($entity->getDocUom() !== null) {
            $snapshot->docUom = $entity->getDocUom()->getId();
        }

        // $snapshot->warehouse = $entity->getWarehouse();
        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
        }

        return $snapshot;
    }
    
   
}
