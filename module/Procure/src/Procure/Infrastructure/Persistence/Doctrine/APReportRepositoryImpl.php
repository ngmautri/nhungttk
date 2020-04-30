<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Mapper\ApMapper;
use Procure\Infrastructure\Persistence\APReportRepositoryInterface;
use Procure\Infrastructure\Persistence\SQL\ApSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APReportRepositoryImpl extends AbstractDoctrineRepository implements APReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\APReportRepositoryInterface::getList()
     */
    public function getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $results = $this->_getList($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\FinVendorInvoice $doctrineRootEntity ;*/
            $doctrineRootEntity = $r[0];

            $rootSnapshot = ApMapper::createDetailSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($rootSnapshot == null) {
                continue;
            }

            $rootSnapshot->totalRows = $r["total_row"];
            $rootSnapshot->netAmount = $r["net_amount"];
            $rootSnapshot->taxAmount = $r["tax_amount"];
            $rootSnapshot->grossAmount = $r["gross_amount"];
            $rootSnapshot->discountAmount = $r["gross_discount_amount"];
            $resultList[] = $rootSnapshot;
        }

        return $resultList;
    }

    public function getListTotal($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        return $this->_getListTotal($is_active, $current_state, $docStatus, $filter_by, $sort_by, $sort, $limit, $offset);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\APReportRepositoryInterface::getAllAPRowStatus()
     */
    public function getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset)
    {
        $results = $this->_getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = array();
        foreach ($results as $r) {

            /**@var \Application\Entity\FinVendorInvoiceRow $entity ;*/
            $entity = $r[0];

            $snapShot = ApMapper::createRowDetailSnapshot($this->getDoctrineEM(), $entity);

            if ($snapShot == null) {
                continue;
            }

            $snapShot->invoiceToken = $r["invoice_token"];
            $snapShot->sapNumber = $r["sap_doc"];

            $snapShot->vendorName = $r["vendor_name"];
            $snapShot->localCurrencyId = $r["local_currency_id"];
            $snapShot->docCurrencyId = $r["doc_currency_id"];
            $snapShot->docCurrencyISO = $r["currency_iso3"];
            $snapShot->exchangeRate = $r["exchange_rate"];
            $snapShot->apDocStatus = $r["ap_doc_status"];
            $snapShot->postingYear = $r["posting_year"];
            $snapShot->postingMonth = $r["posting_month"];
            $snapShot->prId = $r["pr_id"];
            $snapShot->prToken = $r["pr_token"];
            $snapShot->prNumber = $r["pr_number"];
            $snapShot->prSysNumber = $r["pr_auto_number"];

            $snapShot->prDate = $r["pr_date"];

            $snapShot->poId = $r["po_id"];
            $snapShot->poToken = $r["po_token"];
            $snapShot->poNumber = $r["contract_no"];
            $snapShot->poSysNumber = $r["po_sys_number"];
            $snapShot->poDate = $r["po_date"];

            $snapShot->itemName = $r["item_name"];
            $snapShot->itemToken = $r["item_token"];
            $snapShot->itemNumber = $r["item_sys_number"];
            $snapShot->itemSKU = $r["item_sku"];
            $snapShot->itemSKU1 = $r["item_sku1"];
            $snapShot->itemSKU2 = $r["item_sku2"];

            $resultList[] = $snapShot;
        }

        return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\APReportRepositoryInterface::getAllAPRowStatusTotal()
     */
    public function getAllAPRowStatusTotal($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset)
    {
        return $this->_getAllAPRowStatusTotal($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset);
    }

    private function _getAllAPRowStatusTotal($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset)
    {
        $sql = ApSQL::AP_ROW_TOTAL_SQL;

        $sql_tmp = "";
        if ($vendor_id > 0) {
            $sql_tmp = $sql_tmp . " AND fin_vendor_invoice.vendor_id=" . $vendor_id;
        }

        if ($item_id > 0) {
            $sql_tmp = $sql_tmp . " AND fin_vendor_invoice_row.item_id.item_id=" . $item_id;
        }

        if ($ap_year > 0) {
            $sql_tmp = $sql_tmp . " AND YEAR(fin_vendor_invoice.posting_date)=" . $ap_year;
        }

        if ($ap_month > 0) {
            $sql_tmp = $sql_tmp . " AND MONTH(fin_vendor_invoice.posting_date)=" . $ap_month;
        }

        $sql = $sql . $sql_tmp;

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addScalarResult("total_rows", "total_rows");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();

            if (count($result) == 1) {
                return (int) $result[0]['total_rows'];
            }
        } catch (NoResultException $e) {}
        return 0;
    }

    private function _getAllAPRowStatus($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset)
    {
        $sql = ApSQL::AP_ROW_SQL;

        $sql_tmp = '';

        if ($vendor_id > 0) {
            $sql_tmp = $sql_tmp . " AND fin_vendor_invoice.vendor_id=" . $vendor_id;
        }

        if ($item_id > 0) {
            $sql_tmp = $sql_tmp . " AND fin_vendor_invoice_row.item_id.item_id=" . $item_id;
        }

        if ($ap_year > 0) {
            $sql_tmp = $sql_tmp . " AND YEAR(fin_vendor_invoice.posting_date)=" . $ap_year;
        }

        if ($ap_month > 0) {
            $sql_tmp = $sql_tmp . " AND MONTH(fin_vendor_invoice.posting_date)=" . $ap_month;
        }

        switch ($sort_by) {
            /*
             * case "itemName":
             * $sql_tmp1 = $sql_tmp1 . " ORDER BY nmt_inventory_item.item_name " . $sort;
             * break;
             */
            case "vendorName":
                $sql_tmp = $sql_tmp . " ORDER BY fin_vendor_invoice.vendor_name " . $sort;
                break;

            case "itemName":
                $sql_tmp = $sql_tmp . " ORDER BY nmt_inventory_item.item_name " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql_tmp = $sql_tmp . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = $sql . $sql_tmp;

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRow', 'fin_vendor_invoice_row');
            $rsm->addScalarResult("invoice_id", "invoice_id");
            $rsm->addScalarResult("vendor_id", "vendor_id");
            $rsm->addScalarResult("vendor_name", "vendor_name");
            $rsm->addScalarResult("invoice_token", "invoice_token");

            $rsm->addScalarResult("sap_doc", "sap_doc");

            $rsm->addScalarResult("posting_year", "posting_year");
            $rsm->addScalarResult("posting_month", "posting_month");
            $rsm->addScalarResult("posting_date", "posting_date");
            $rsm->addScalarResult("contract_date", "contract_date");
            $rsm->addScalarResult("ap_doc_status", "ap_doc_status");

            $rsm->addScalarResult("doc_currency_id", "doc_currency_id");
            $rsm->addScalarResult("currency_id", "currency_id");
            $rsm->addScalarResult("currency_iso3", "currency_iso3");
            $rsm->addScalarResult("local_currency_id", "local_currency_id");
            $rsm->addScalarResult("exchange_rate", "exchange_rate");

            $rsm->addScalarResult("item_token", "item_token");
            $rsm->addScalarResult("item_sys_number", "item_sys_number");
            $rsm->addScalarResult("item_name", "item_name");
            $rsm->addScalarResult("item_sku", "item_sku");
            $rsm->addScalarResult("item_sku1", "item_sku1");
            $rsm->addScalarResult("item_sku2", "item_sku2");

            $rsm->addScalarResult("pr_id", "pr_id");
            $rsm->addScalarResult("pr_token", "pr_token");
            $rsm->addScalarResult("pr_number", "pr_number");
            $rsm->addScalarResult("pr_auto_number", "pr_auto_number");
            $rsm->addScalarResult("pr_date", "pr_date");

            $rsm->addScalarResult("po_id", "po_id");
            $rsm->addScalarResult("po_token", "po_token");
            $rsm->addScalarResult("contract_no", "contract_no");
            $rsm->addScalarResult("po_sys_number", "po_sys_number");
            $rsm->addScalarResult("po_date", "po_date");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }

    private function _getList($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = ApSQL::AP_LIST;

        if ($docStatus == "all") {
            $docStatus = null;
        }

        if ($is_active == 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active = 1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active = 0";
        }

        if ($current_state != null) {
            $sql = $sql . " AND fin_vendor_invoice.current_state = '" . $current_state . "'";
        }

        if ($docStatus != null) {
            $sql = $sql . " AND fin_vendor_invoice.doc_status = '" . $docStatus . "'";
        }

        $sql = $sql . " GROUP BY fin_vendor_invoice.id";

        switch ($sort_by) {
            case "sysNumber":
                $sql = $sql . " ORDER BY fin_vendor_invoice.sys_number " . $sort;
                break;
            case "docDate":
                $sql = $sql . " ORDER BY fin_vendor_invoice.doc_date " . $sort;
                break;
            case "grossAmount":
                $sql = $sql . " ORDER BY SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.gross_amount) ELSE 0 END) " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY fin_vendor_invoice.created_on " . $sort;
                break;
            case "vendorName":
                $sql = $sql . " ORDER BY fin_vendor_invoice.vendor_name " . $sort;
                break;
            case "currencyCode":
                $sql = $sql . " ORDER BY fin_vendor_invoice.currency_iso3 " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("gross_discount_amount", "gross_discount_amount");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);

            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    private function _getListTotal($is_active = 1, $current_state = null, $docStatus = null, $filter_by = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = ApSQL::AP_LIST;

        if ($docStatus == "all") {
            $docStatus = null;
        }

        if ($is_active == 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active = 1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active = 0";
        }

        if ($current_state != null) {
            $sql = $sql . " AND fin_vendor_invoice.current_state = '" . $current_state . "'";
        }

        if ($docStatus != null) {
            $sql = $sql . " AND fin_vendor_invoice.doc_status = '" . $docStatus . "'";
        }

        $sql = $sql . " GROUP BY fin_vendor_invoice.id";

        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return count($result);
        } catch (NoResultException $e) {
            return 0;
        }
    }
}
