<?php
namespace HR\Infrastructure\Persistance\Domain\Doctrine\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Infrastructure\Doctrine\SQL\TrxSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndividualHelper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param int $id
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getRowsById(EntityManager $doctrineEM, $id)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        $sql = "select * from nmt_inventory_trx where 1%s";
        $tmp1 = sprintf(" AND nmt_inventory_trx.movement_id=%s AND nmt_inventory_trx.is_active=1", $id);
        $sql = sprintf($sql, $tmp1);
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryTrx', 'nmt_inventory_trx');

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param int $id
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getDetailRowsById(EntityManager $doctrineEM, $id)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        $sql = TrxSQL::TRX_ROWS_DETAIL;
        $tmp1 = sprintf("AND nmt_inventory_trx.movement_id=%s AND nmt_inventory_trx.is_active=1", $id);
        $sql = sprintf($sql, $tmp1);
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryTrx', 'nmt_inventory_trx');
            $rsm->addScalarResult("onhand_qty", "onhand_qty");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param int $id
     * @param string $token
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getHeaderByTokenId(EntityManager $doctrineEM, $id, $token)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        $sql = "SELECT
*
FROM
nmt_inventory_mv
LEFT JOIN
(
    SELECT
    	nmt_inventory_mv.id AS movement_id,
    	SUM(CASE WHEN (nmt_inventory_trx.is_active=1) THEN  1 ELSE 0 END) AS total_active_row,
        SUM(CASE WHEN (nmt_inventory_trx.doc_status='posted') THEN  1 ELSE 0 END) AS total_posted_row,
        SUM(CASE WHEN (nmt_inventory_trx.doc_status='draft') THEN  1 ELSE 0 END) AS total_draft_row,
        SUM(CASE WHEN (nmt_inventory_trx.is_active=1) THEN nmt_inventory_trx.net_amount ELSE 0 END) AS total_net_amount,
    	SUM(CASE WHEN (nmt_inventory_trx.is_active=1) THEN nmt_inventory_trx.gross_amount ELSE 0 END) AS total_gross_amount
    FROM nmt_inventory_mv
    JOIN nmt_inventory_trx
    ON nmt_inventory_mv.id = nmt_inventory_trx.movement_id
    WHERE 1 %s GROUP BY nmt_inventory_mv.id
) AS nmt_inventory_trx
ON nmt_inventory_trx.movement_id = nmt_inventory_mv.id
WHERE 1 %s
";

        $tmp1 = sprintf(" AND nmt_inventory_trx.movement_id=%s", $id);
        $tmp2 = sprintf(" AND nmt_inventory_mv.id=%s", $id);

        $sql = sprintf($sql, $tmp1, $tmp2);
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryMv', 'nmt_inventory_mv');
            $rsm->addScalarResult("total_active_row", "total_active_row");
            $rsm->addScalarResult("total_posted_row", "total_posted_row");
            $rsm->addScalarResult("total_draft_row", "total_draft_row");
            $rsm->addScalarResult("total_net_amount", "total_net_amount");
            $rsm->addScalarResult("total_gross_amount", "total_gross_amount");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    static public function getDetailHeaderByTokenId(EntityManager $doctrineEM, $id, $token)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        $sql = TrxSQL::MV_DETAIL;

        $tmp1 = sprintf("AND nmt_inventory_trx.movement_id=%s", $id);
        $tmp2 = sprintf("AND nmt_inventory_mv.id=%s", $id);

        $sql = sprintf($sql, $tmp1, $tmp2);
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryMv', 'nmt_inventory_mv');
            $rsm->addScalarResult("total_active_row", "total_active_row");
            $rsm->addScalarResult("total_posted_row", "total_posted_row");
            $rsm->addScalarResult("total_draft_row", "total_draft_row");
            $rsm->addScalarResult("total_net_amount", "total_net_amount");
            $rsm->addScalarResult("total_gross_amount", "total_gross_amount");

            $rsm->addScalarResult("total_rows", "total_rows");
            $rsm->addScalarResult("zero_qty_rows", "zero_qty_rows");
            $rsm->addScalarResult("un_used_rows", "un_used_rows");
            $rsm->addScalarResult("exhausted_rows", "exhausted_rows");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
