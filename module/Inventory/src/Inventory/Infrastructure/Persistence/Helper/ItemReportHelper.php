<?php
namespace Inventory\Infrastructure\Persistence\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Persistence\Filter\ApReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\ApSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportHelper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getList(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof ApReportSqlFilter) {
            return null;
        }

        $sql = ApSQL::AP_LIST;

        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getIsActive() == 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active = 1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql = $sql . " AND fin_vendor_invoice.is_active = 0";
        }

        if ($filter->getCurrentState() != null) {
            $sql = $sql . " AND fin_vendor_invoice.current_state = '" . $filter->getCurrentState() . "'";
        }

        if ($filter->getDocStatus() != null) {
            $sql = $sql . \sprintf(' AND fin_vendor_invoice.doc_status ="%s"', $filter->getDocStatus());
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
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoice', 'fin_vendor_invoice');
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("max_row_number", "max_row_number");
            $rsm->addScalarResult("net_amount", "net_amount");
            $rsm->addScalarResult("tax_amount", "tax_amount");
            $rsm->addScalarResult("gross_amount", "gross_amount");
            $rsm->addScalarResult("gross_discount_amount", "gross_discount_amount");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);

            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
