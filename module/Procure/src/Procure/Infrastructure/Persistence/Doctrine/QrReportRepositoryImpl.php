<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Mapper\QrMapper;
use Procure\Infrastructure\Persistence\QrReportRepositoryInterface;
use Procure\Infrastructure\Persistence\Helper\PoReportHelper;
use Procure\Infrastructure\Persistence\Helper\QrReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QrReportRepositoryImpl extends AbstractDoctrineRepository implements QrReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\QrReportRepositoryInterface::getOfItem()
     */
    public function getOfItem($itemId, $itemToken)
    {
        $sql = "
SELECT
    nmt_inventory_item.item_name as item_name,
	nmt_procure_qo_row.*
FROM nmt_procure_qo_row

LEFT JOIN nmt_procure_qo
ON nmt_procure_qo.id = nmt_procure_qo_row.qo_id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_qo_row.item_id
WHERE 1
";

        // $sql = $sql . " AND nmt_inventory_item.id =" . $item_id;

        $sql = $sql . sprintf(" AND nmt_inventory_item.id =%s AND nmt_inventory_item.token='%s'", $item_id, $token);
        $sql = $sql . " ORDER BY nmt_procure_qo.contract_date DESC ";
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcureQoRow', 'nmt_procure_qo_row');
            $rsm->addScalarResult("item_name", "item_name");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();

            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\QrReportRepositoryInterface::getList()
     */
    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $results = QrReportHelper::getList($this->doctrineEM, $filter, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcureQo $po ;*/
            $doctrineRootEntity = $r[0];

            $rootSnapshot = QrMapper::createSnapshot($this->doctrineEM, $doctrineRootEntity);

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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\QrReportRepositoryInterface::getListTotal()
     */
    public function getListTotal(SqlFilterInterface $filter)
    {
        return QrReportHelper::getListTotal($this->doctrineEM, $filter);
    }

    public function getAllRowTotal(SqlFilterInterface $filter)
    {}

    public function getAllRow(SqlFilterInterface $filter, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        $results = QrReportHelper::getAllRow($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcureQoRow $doctrineRootEntity ;*/
            $doctrineRootEntity = $r;

            $localSnapshot = QrMapper::createRowSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($localSnapshot == null) {
                continue;
            }

            $resultList[] = $localSnapshot;
        }

        return $resultList;
    }
}
