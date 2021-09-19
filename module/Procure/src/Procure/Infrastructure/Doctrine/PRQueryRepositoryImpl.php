<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Domain\PurchaseRequest\Factory\PrFactory;
use Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface;
use Procure\Domain\Shared\Constants;
use Procure\Infrastructure\Doctrine\SQL\PrSQL;
use Procure\Infrastructure\Mapper\PrMapper;

/**
 *
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRQueryRepositoryImpl extends AbstractDoctrineRepository implements PrQueryRepositoryInterface
{

    /**
     *
     * @param int $id
     * @return NULL|\Application\Entity\NmtProcurePr
     */
    private function _getHeaderEntityById($id)
    {
        if ($id == null) {
            return null;
        }

        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcurePr $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePr')->findOneBy($criteria);
        return $entity;
    }

    /**
     *
     * @param int $rowId
     * @return NULL|\Application\Entity\NmtProcurePr
     */
    private function _getHeaderEntityByRowId($rowId)
    {
        $criteria = array(
            'id' => $rowId
        );

        /**
         *
         * @var \Application\Entity\NmtProcurePrRow $doctrineEntity ;
         */
        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePrRow')->findOneBy($criteria);
        if ($doctrineEntity == null) {
            return null;
        }

        return $doctrineEntity->getPr();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getHeaderSnapshotById()
     */
    public function getHeaderSnapshotById($id)
    {
        return PrMapper::createSnapshot($this->getDoctrineEM(), $this->_getHeaderEntityById($id));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getHeaderSnapshotByRowId()
     */
    public function getHeaderSnapshotByRowId($rowId)
    {
        $doctrineEntity = $this->_getHeaderEntityByRowId($rowId);

        if ($doctrineEntity == null) {
            return null;
        }

        return PrMapper::createSnapshot($this->getDoctrineEM(), $doctrineEntity);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getHeaderIdByRowId()
     */
    public function getHeaderIdByRowId($id)
    {
        $doctrineEntity = $this->_getHeaderEntityByRowId($id);

        if ($doctrineEntity == null) {
            return null;
        }

        return $doctrineEntity->getId();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $doctrineEntity = $this->_getHeaderEntityById($id);
        if ($doctrineEntity != null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getVersionArray()
     */
    public function getVersionArray($id, $token = null)
    {
        $doctrineEntity = $this->_getHeaderEntityById($id);
        if ($doctrineEntity !== null) {
            return [
                "revisionNo" => $doctrineEntity->getRevisionNo(),
                "docVersion" => $doctrineEntity->getRevisionNo() // Todo
            ];
        }

        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getHeaderById()
     */
    public function getHeaderById($id, $token = null)
    {
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**
         *
         * @var \Application\Entity\NmtProcurePr $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePr')->findOneBy($criteria);
        $snapshot = PrMapper::createSnapshot($this->doctrineEM, $entity);

        if ($snapshot == null) {
            return null;
        }

        return PrFactory::constructFromDB($snapshot);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getRootEntityByTokenId()
     */
    public function getRootEntityByTokenId($id, $token = null)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtProcurePr')
            ->findOneBy($criteria);

        return $this->_createRootEntity($rootEntityDoctrine, $id);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface::getRootEntityById()
     */
    public function getRootEntityById($id)
    {
        $rootEntityDoctrine = $this->_getHeaderEntityById($id);
        return $this->_createRootEntity($rootEntityDoctrine, $id);
    }

    /**
     *
     * @param object $rootEntityDoctrine
     * @param int $id
     * @return NULL|void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    private function _createRootEntity($rootEntityDoctrine, $id)
    {
        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = PrMapper::createSnapshot($this->getDoctrineEM(), $rootEntityDoctrine);
        if ($rootSnapshot == null) {
            return null;
        }

        $rows = $this->getRowsById($id);

        if (count($rows) == 0) {
            $rootEntity = PrFactory::constructFromDB($rootSnapshot);
            return $rootEntity;
        }

        $totalRows = 0;
        $totalActiveRows = 0;
        $completedRows = 0;
        $netAmount = 0;
        $taxAmount = 0;
        $grossAmount = 0;
        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcurePrRow $localEnityDoctrine ;*/
            $localEnityDoctrine = $r[0];

            $localSnapshot = PrMapper::createRowSnapshot($this->getDoctrineEM(), $localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }
            $totalRows ++;
            $localSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_UNCOMPLETED;

            $localSnapshot->draftPoQuantity = $r["po_qty"];
            $localSnapshot->postedPoQuantity = $r["posted_po_qty"];

            $localSnapshot->draftGrQuantity = $r["gr_qty"];
            $localSnapshot->postedGrQuantity = $r["posted_gr_qty"];

            $localSnapshot->draftApQuantity = $r["ap_qty"];
            $localSnapshot->postedApQuantity = $r["posted_ap_qty"];

            $localSnapshot->draftStockQrQuantity = $r["stock_gr_qty"];
            $localSnapshot->postedStockQrQuantity = $r["posted_stock_gr_qty"];

            $localSnapshot->setLastVendorName($r["vendor_name"]);
            $localSnapshot->setLastUnitPrice($r["last_unit_price"]);
            $localSnapshot->setLastCurrency($r["last_currency_iso3"]);

            if ($localSnapshot->postedGrQuantity >= $localSnapshot->getDocQuantity()) {
                $completedRows ++;
                $localSnapshot->transactionStatus = Constants::TRANSACTION_STATUS_COMPLETED;
            }

            $localEntity = PRRow::makeFromSnapshot($localSnapshot);

            $docRowsArray[] = $localEntity;
            $rowIdArray[] = $localEntity->getId();

            // break;
        }

        $rootSnapshot->completedRows = $completedRows;
        $rootSnapshot->totalRows = $totalRows;
        $rootSnapshot->totalActiveRows = $totalActiveRows;
        $rootSnapshot->netAmount = $netAmount;
        $rootSnapshot->taxAmount = $taxAmount;
        $rootSnapshot->grossAmount = $grossAmount;

        $rootEntity = PrFactory::constructFromDB($rootSnapshot);

        $rootEntity->setDocRows($docRowsArray);
        $rootEntity->setRowIdArray($rowIdArray);
        return $rootEntity;
    }

    private function getRowsById($id)
    {
        $sql = "
SELECT
	nmt_procure_pr_row.*,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,
    IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
    IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,
        
    IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,
        
    IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,
        
    IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,
        
    last_ap.vendor_name as vendor_name,
    last_ap.unit_price as last_unit_price,
    last_ap.currency_iso3 as last_currency_iso3        
FROM nmt_procure_pr_row
        
LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

LEFT JOIN
(
%s   
)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
%s   
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
%s   
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
%s   
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
%s   
)
as last_ap
on last_ap.item_id = nmt_procure_pr_row.item_id

WHERE nmt_procure_pr_row.pr_id=%s AND nmt_procure_pr_row.is_active=1 order by nmt_procure_pr_row.row_number
";

        $tmp1 = sprintf(" AND nmt_procure_pr_row.pr_id=%s AND nmt_procure_pr_row.is_active=1", $id);
        $sql1 = sprintf(PrSQL::PR_PO_ROW, $tmp1);
        $sql2 = sprintf(PrSQL::PR_AP_ROW, $tmp1);
        $sql3 = sprintf(PrSQL::PR_GR_ROW, $tmp1);
        $sql4 = sprintf(PrSQL::PR_STOCK_GR_ROW, $tmp1);
        $sql5 = sprintf(PrSQL::ITEM_LAST_AP_ROW, $tmp1);

        $sql = sprintf($sql, $sql1, $sql2, $sql3, $sql4, $sql5, $id);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $rsm->addScalarResult("po_qty", "po_qty");
            $rsm->addScalarResult("posted_po_qty", "posted_po_qty");

            $rsm->addScalarResult("gr_qty", "gr_qty");
            $rsm->addScalarResult("posted_gr_qty", "posted_gr_qty");

            $rsm->addScalarResult("ap_qty", "ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");

            $rsm->addScalarResult("stock_gr_qty", "stock_gr_qty");
            $rsm->addScalarResult("posted_stock_gr_qty", "posted_stock_gr_qty");

            $rsm->addScalarResult("vendor_name", "vendor_name");
            $rsm->addScalarResult("last_unit_price", "last_unit_price");
            $rsm->addScalarResult("last_currency_iso3", "last_currency_iso3");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
