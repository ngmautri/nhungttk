<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Application\DTO\Pr\PrHeaderDetailDTO;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Mapper\PrMapper;
use Procure\Infrastructure\Persistence\PrReportRepositoryInterface;
use Procure\Infrastructure\Persistence\Helper\PrReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrReportRepositoryImpl extends AbstractDoctrineRepository implements PrReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PrReportRepositoryInterface::getListWithCustomDTO()
     */
    public function getListWithCustomDTO(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        $results = PrReportHelper::getList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            yield null;
        }

        // $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcurePr $po ;*/

            $doctrineRootEntity = $r[0];

            /**
             *
             * @var PrHeaderDetailDTO $dto ;
             */
            $dto = PrMapper::createSnapshot($this->doctrineEM, $doctrineRootEntity, new PrHeaderDetailDTO());
            if ($dto == null) {
                continue;
            }

            $dto->totalRows = $r["total_row"];
            $dto->grCompletedRows = $r["gr_completed"];
            $dto->apCompletedRows = $r["ap_completed"];
            $dto->grPartialCompletedRows = $r["gr_partial_completed"];
            $dto->apPartialCompletedRows = $r["ap_partial_completed"];
            yield $dto;
        }
        // return $resultList;
    }

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
     * @see \Procure\Infrastructure\Persistence\PrReportRepositoryInterface::getList()
     */
    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        $results = PrReportHelper::getList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            yield null;
        }

        // $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcurePr $po ;*/
            $doctrineRootEntity = $r[0];

            $rootSnapshot = PrMapper::createSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($rootSnapshot == null) {
                continue;
            }

            $rootSnapshot->totalRows = $r["total_row"];
            yield $rootSnapshot;
        }

        // return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PrReportRepositoryInterface::getListTotal()
     */
    public function getListTotal(SqlFilterInterface $filter)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object");
        }

        return PrReportHelper::getListTotal($this->getDoctrineEM(), $filter);
    }

    public function getAllRowTotal(SqlFilterInterface $filter)
    {
        return PrReportHelper::getAllRowTotal($this->getDoctrineEM(), $filter);
    }

    public function getAllRowWithCustomDTO(SqlFilterInterface $filter)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PrReportRepositoryInterface::getAllRow()
     */
    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof SqlFilterInterface) {
            throw new \InvalidArgumentException("Invalid filter object.");
        }

        $results = PrReportHelper::getAllRow($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
        if (count($results) == null) {
            yield null;
        }

        // $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcurePrRow $po ;*/
            $doctrineRootEntity = $r[0];
            $localSnapshot = PrMapper::createRowSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($localSnapshot == null) {
                continue;
            }

            // $localSnapshot = new PRRowSnapshot();

            $localSnapshot->draftPoQuantity = $r["po_qty"];
            $localSnapshot->postedPoQuantity = $r["posted_po_qty"];

            $localSnapshot->draftGrQuantity = $r["gr_qty"];
            $localSnapshot->postedGrQuantity = $r["posted_gr_qty"];

            $localSnapshot->draftApQuantity = $r["ap_qty"];
            $localSnapshot->postedApQuantity = $r["posted_ap_qty"];

            $localSnapshot->draftStockQrQuantity = $r["stock_gr_qty"];
            $localSnapshot->postedStockQrQuantity = $r["posted_stock_gr_qty"];
            $localSnapshot->lastVendorName = $r["last_vendor_name"];
            $localSnapshot->lastUnitPrice = $r["last_unit_price"];
            $localSnapshot->lastCurrency = $r["currency_iso3"];

            yield $localSnapshot;
        }

        // return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PrReportRepositoryInterface::getRawList()
     */
    public function getRawList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        return PrReportHelper::getList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PrReportRepositoryInterface::getAllRawRow()
     */
    public function getAllRawRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        return PrReportHelper::getAllRow($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
    }
}