<?php
namespace Procure\Infrastructure\Doctrine\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Doctrine\SQL\GrSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GrHelper
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
    static public function getRows(EntityManager $doctrineEM, $id, $sort_by)
    {
        if ($sort_by == null) {
            $sort_by = 'row_number';
        }

        $sql = "
SELECT
*
FROM nmt_procure_gr_row

LEFT JOIN
(%s)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.gr_row_id = nmt_procure_gr_row.id

WHERE 1 %s %s";

        /**
         *
         * @todo To add Return and Credit Memo
         */

        $where = sprintf(" AND nmt_procure_gr_row.gr_id=%s AND nmt_procure_gr_row.is_active=1", $id);

        $order_by = '';
        if ($sort_by != null) {
            $order_by = sprintf(" ORDER BY %s", $sort_by);
        }
        $sql1 = sprintf(GrSQL::SQL_ROW_GR_AP, $where);

        $sql = sprintf($sql, $sql1, $where, $order_by);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureGrRow', 'nmt_procure_gr_row');
            $rsm->addScalarResult("draft_ap_qty", "draft_ap_qty");
            $rsm->addScalarResult("posted_ap_qty", "posted_ap_qty");
            $rsm->addScalarResult("confirmed_ap_balance", "confirmed_ap_balance");
            $rsm->addScalarResult("open_ap_qty", "open_ap_qty");
            $rsm->addScalarResult("billed_amount", "billed_amount");
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
