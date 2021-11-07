<?php
namespace Inventory\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Domain\Item\Serial\Factory\ItemSerialFactory;
use Inventory\Infrastructure\Persistence\Domain\Doctrine\Mapper\ItemSerialMapper;
use Inventory\Infrastructure\Persistence\SQL\ItemSerialSQL;
use Inventory\Infrastructure\Persistence\SQL\Filter\ItemSerialSqlFilter;
use Generator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialHelper
{

    public static function getRows(EntityManager $doctrineEM, ItemSerialSqlFilter $filter)
    {
        if (! $filter instanceof ItemSerialSqlFilter) {
            return null;
        }

        // create SQL
        $sql = self::createFullSQL($filter);
        $sql = $sql . self::createSortBy($filter) . self::createLimitOffset($filter);
        $rsm = self::createFullResultMapping($doctrineEM);
        return self::runQuery($doctrineEM, $sql, $rsm);
    }

    public static function getTotalRows(EntityManager $doctrineEM, ItemSerialSqlFilter $filter)
    {
        if (! $filter instanceof ItemSerialSqlFilter) {
            return null;
        }

        $sql = self::createSQLForTotalCount($filter);
        $f = "select count(*) as total_row from (%s) as t ";
        $sql = sprintf($f, $sql);
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addScalarResult("total_row", "total_row");
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /*
     * |=============================
     * | CREATE Where
     * |
     * |=============================
     */
    public static function createJoinWhere(ItemSerialSqlFilter $filter)
    {
        $tmp1 = '';

        if ($filter->getIsActive() == 1) {
            $tmp1 = $tmp1 . " AND (nmt_inventory_item_serial.is_active = 1";
        } elseif ($filter->getIsActive() == - 1) {
            $tmp1 = $tmp1 . " AND (nmt_inventory_item_serial.is_active = 0 OR nmt_inventory_item_serial.is_active = 0)";
        }

        if ($filter->getDocYear() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND year(fin_vendor_invoice_row.invoice_posting_date) =%s", $filter->getDocYear());
        }
        if ($filter->getDocMonth() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND month(fin_vendor_invoice_row.invoice_posting_date) =%s", $filter->getDocMonth());
        }

        if ($filter->getItemId() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND fin_vendor_invoice_row.item_id =%s", $filter->getItemId());
        }

        if ($filter->getInvoiceId() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND fin_vendor_invoice_row.invoice_id =%s", $filter->getInvoiceId());
        }

        if ($filter->getGrId() > 0) {
            // $tmp1 = $tmp1 . \sprintf(" AND fin_vendor_invoice_row.invoice_id =%s", $filter->getInvoiceId());
        }

        return $tmp1;
    }

    public static function createWhere(ItemSerialSqlFilter $filter)
    {
        $tmp1 = '';
        if ($filter->getIsActive() == 1) {
            $tmp1 = $tmp1 . " AND (nmt_inventory_item_serial.is_active = 1";
        } elseif ($filter->getIsActive() == - 1) {
            $tmp1 = $tmp1 . " AND (nmt_inventory_item_serial.is_active = 0 OR nmt_inventory_item_serial.is_active = 0)";
        }

        if ($filter->getDocYear() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND year(fin_vendor_invoice_row.invoice_posting_date) =%s", $filter->getDocYear());
        }
        if ($filter->getDocMonth() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND month(fin_vendor_invoice_row.invoice_posting_date) =%s", $filter->getDocMonth());
        }

        if ($filter->getItemId() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND nmt_inventory_item_serial.item_id =%s", $filter->getItemId());
        }

        if ($filter->getInvoiceId() > 0) {
            $tmp1 = $tmp1 . \sprintf(" AND fin_vendor_invoice_row.invoice_id =%s", $filter->getInvoiceId());
        }

        if ($filter->getGrId() > 0) {
            // $tmp1 = $tmp1 . \sprintf(" AND fin_vendor_invoice_row.invoice_id =%s", $filter->getInvoiceId());
        }

        return $tmp1;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param array $rows
     * @return Generator
     */
    public static function createRowsGenerator(EntityManager $doctrineEM, $rows)
    {
        if ($rows == null) {
            yield null;
        }

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtInventoryItemSerial $localEnityDoctrine ;*/
            $localEnityDoctrine = $r[0];

            $localSnapshot = ItemSerialMapper::createSerialSnapshot($localEnityDoctrine);

            if ($localSnapshot == null) {
                continue;
            }

            if (isset($r["item_serial_name"])) {
                $localSnapshot->itemName = $r["item_serial_name"];
            }

            if (isset($r["item_token"])) {
                $localSnapshot->itemToken = $r["item_token"];
            }

            if (isset($r["invoice_id"])) {
                $localSnapshot->invoiceId = $r["invoice_id"];
            }

            if (isset($r["invoice_token"])) {
                $localSnapshot->invoiceToken = $r["invoice_token"];
            }

            if (isset($r["invoice_sys_number"])) {
                $localSnapshot->invoiceSysNumber = $r["invoice_sys_number"];
            }

            if (isset($r["invoice_doc_currency"])) {
                // $localSnapshot->in = $r["invoice_doc_currency"];
            }

            $localEntity = ItemSerialFactory::contructFromDB($localSnapshot);

            yield $localEntity;
        }
    }

    /*
     * |=============================
     * | Exe Query
     * |
     * |=============================
     */
    private static function runQuery(EntityManager $doctrineEM, $sql, ResultSetMappingBuilder $rsm)
    {
        // /echo $sql;
        try {
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    private static function exeCountQuery(EntityManager $doctrineEM, $sql)
    {
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /*
     * |=============================
     * |Mapping Result
     * |
     * |=============================
     */

    // map default fiels
    private static function mapDefaultResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItemSerial', 'nmt_inventory_item_serial');
        $rsm->addScalarResult("item_name", "item_name");
        $rsm->addScalarResult("item_id", "item_id");
        $rsm->addScalarResult("item_token", "item_token");

        return $rsm;
    }

    // map ap
    private static function mapApResult(EntityManager $doctrineEM, $rsm)
    {
        $rsm->addScalarResult("invoice_id", "item_token");
        $rsm->addScalarResult("invoice_token", "invoice_token");
        $rsm->addScalarResult("invoice_sys_number", "invoice_sys_number");
        $rsm->addScalarResult("invoice_doc_currency", "invoice_doc_currency");
        $rsm->addScalarResult("invoice_posting_date", "invoice_posting_date");
        return $rsm;
    }

    /*
     * |=============================
     * | Create sql parts.
     * |
     * |=============================
     */
    public static function createFullSQL(ItemSerialSqlFilter $filter)
    {
        $sql = ItemSerialSQL::SERIAL_LIST_TEMPLATE;
        $sql = self::AddApSQL($sql, $filter);
        return sprintf($sql, self::createWhere($filter));
    }

    public static function createFullResultMapping(EntityManager $doctrineEM)
    {
        $rsm = new ResultSetMappingBuilder($doctrineEM);
        $rsm = self::mapDefaultResult($doctrineEM, $rsm);
        $rsm = self::mapApResult($doctrineEM, $rsm);
        return $rsm;
    }

    public static function createSQLForTotalCount(ItemSerialSqlFilter $filter)
    {
        $sql = ItemSerialSQL::SERIAL_LIST_TEMPLATE;
        $sql = self::AddApSQL($sql, $filter);
        return sprintf($sql, self::createWhere($filter));
    }

    /*
     * |=============================
     * | Create sql parts.
     * |
     * |=============================
     */
    private static function AddApSQL($sql, ItemSerialSqlFilter $filter)
    {
        $select = "
    fin_vendor_invoice_row.vendor_id,
    fin_vendor_invoice_row.vendor_name,
    fin_vendor_invoice_row.invoice_id,
	fin_vendor_invoice_row.invoice_token,
    fin_vendor_invoice_row.invoice_sys_number,
    fin_vendor_invoice_row.invoice_doc_currency,
	fin_vendor_invoice_row.invoice_posting_date,";

        $sql = str_replace(ItemSerialSQL::SELECT_AP_KEY, $select, $sql);

        $join = sprintf(ItemSerialSQL::SERIAL_AP_SQL, self::createJoinWhere($filter));
        $sql = str_replace(ItemSerialSQL::JOIN_AP_KEY, $join, $sql);
        return $sql;
    }

    /*
     * |=============================
     * | SORT AND LIMIT
     * |
     * |=============================
     */
    private static function createSortBy(ItemSerialSqlFilter $filter)
    {
        $tmp = '';
        switch ($filter->getSortBy()) {
            case "itemName":
                $tmp = $tmp . " ORDER BY nmt_inventory_item.item_name " . $filter->getSort();
                break;

            case "createdDate":
                $tmp = $tmp . " ORDER BY nmt_inventory_item_serial.created_on " . $filter->getSort();
                break;
        }

        return $tmp;
    }

    private static function createLimitOffset(ItemSerialSqlFilter $filter)
    {
        $tmp = '';

        if ($filter->getLimit() > 0) {
            $tmp = $tmp . " LIMIT " . $filter->getLimit();
        }

        if ($filter->getOffset() > 0) {
            $tmp = $tmp . " OFFSET " . $filter->getOffset();
        }

        return $tmp;
    }
}
