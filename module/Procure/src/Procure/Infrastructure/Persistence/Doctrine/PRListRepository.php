<?php
namespace Procure\Infrastructure\Persistence\Doctrine;


use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Persistence\PRListRepositoryInterface;
use Procure\Infrastructure\Persistence\SQL\PrSQL;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRListRepository extends AbstractDoctrineRepository implements PRListRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PRListRepositoryInterface::getAllPrRow()
     */
    public function getAllPrRow($is_active = 1, $pr_year, $balance = null, $sort_by = null, $sort = "ASC", $limit, $offset)
    {
        $sql = PrSQL::PR_ROW_SQL;
        $sql_tmp = '';

        $sql_tmp1 = '';
        if ($is_active == 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr.is_active = 1 AND nmt_procure_pr_row.is_active = 1)";
        } elseif ($is_active == - 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
        }

        if ($pr_year > 0) {
            $sql_tmp1 = $sql_tmp1 . " AND year(nmt_procure_pr.created_on) =" . $pr_year;
        }

        if ($balance == 0) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr_row.quantity -  IFNULL(nmt_procure_gr_row.posted_gr_qty,0)) <= 0";
        }
        if ($balance == 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr_row.quantity -  IFNULL(nmt_procure_gr_row.posted_gr_qty,0)) > 0";
        }
        if ($balance == - 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr_row.quantity -  IFNULL(nmt_procure_gr_row.posted_gr_qty,0)) < 0";
        }

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

        if ($limit > 0) {
            $sql_tmp1 = $sql_tmp1 . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql_tmp1 = $sql_tmp1 . " OFFSET " . $offset;
        }

        $sql = sprintf($sql, $sql_tmp1);
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
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PRListRepositoryInterface::getAllPrRowTotal()
     */
    public function getAllPrRowTotal($is_active = 1, $pr_year, $balance = null, $sort_by = null, $sort = "ASC", $limit, $offset)
    {
        $sql = PrSQL::PR_ROW_SQL;
        $sql_tmp = '';

        $sql_tmp1 = '';
        if ($is_active == 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr.is_active = 1 AND nmt_procure_pr_row.is_active = 1)";
        } elseif ($is_active == - 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
        }

        if ($pr_year > 0) {
            $sql_tmp1 = $sql_tmp1 . " AND year(nmt_procure_pr.created_on) =" . $pr_year;
        }

        if ($balance == 0) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr_row.quantity -  IFNULL(nmt_procure_gr_row.posted_gr_qty,0)) <= 0";
        }
        if ($balance == 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr_row.quantity -  IFNULL(nmt_procure_gr_row.posted_gr_qty,0)) > 0";
        }
        if ($balance == - 1) {
            $sql_tmp1 = $sql_tmp1 . " AND (nmt_procure_pr_row.quantity -  IFNULL(nmt_procure_gr_row.posted_gr_qty,0)) < 0";
        }

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

        if ($limit > 0) {
            $sql_tmp1 = $sql_tmp1 . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql_tmp1 = $sql_tmp1 . " OFFSET " . $offset;
        }

        $sql = sprintf($sql, $sql_tmp1);
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

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return count($result);
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PRListRepositoryInterface::getPrStatus()
     */
    public function getPrStatus($prId, $balance, $sort_by = null, $sort = "ASC")
    {}
}
