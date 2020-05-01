<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Mapper\PoMapper;
use Procure\Infrastructure\Persistence\PrReportRepositoryInterface;
use Procure\Infrastructure\Persistence\Helper\PoReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoReportRepositoryImpl extends AbstractDoctrineRepository implements PrReportRepositoryInterface
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

        $results = PoReportHelper::getList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcurePo $po ;*/

            $doctrineRootEntity = $r[0];

            /**
             *
             * @var PoDetailsDTO $dto ;
             */
            $dto = PoMapper::createDetailSnapshot($this->doctrineEM, $doctrineRootEntity, new PoDetailsDTO());
            if ($dto == null) {
                continue;
            }

            $dto->totalRows = $r["total_row"];
            $dto->totalActiveRows = $r["active_row"];
            $dto->netAmount = $r["net_amount"];
            $dto->taxAmount = $r["tax_amount"];
            $dto->grossAmount = $r["gross_amount"];
            $dto->billedAmount = $r["billed_amount"];
            $resultList[] = $dto;
        }
        return $resultList;
    }

    public function getOfItem($itemId, $itemToken)
    {}

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

        $results = PoReportHelper::getList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcurePo $po ;*/
            $doctrineRootEntity = $r[0];

            $rootSnapshot = PoMapper::createSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($rootSnapshot == null) {
                continue;
            }

            $rootSnapshot->totalRows = $r["total_row"];
            $rootSnapshot->totalActiveRows = $r["active_row"];
            $rootSnapshot->netAmount = $r["net_amount"];
            $rootSnapshot->taxAmount = $r["tax_amount"];
            $rootSnapshot->grossAmount = $r["gross_amount"];
            $rootSnapshot->billedAmount = $r["billed_amount"];
            $resultList[] = $rootSnapshot;
        }

        return $resultList;
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

        return PoReportHelper::getListTotal($this->getDoctrineEM(), $filter);
    }

    public function getAllRowTotal(SqlFilterInterface $filter)
    {
        return PoReportHelper::getAllRowTotal($this->getDoctrineEM(), $filter);
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

        $results = PoReportHelper::getAllRow($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtProcurePoRow $doctrineRootEntity ;*/
            $doctrineRootEntity = $r[0];

            $localSnapshot = PoMapper::createRowSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($localSnapshot == null) {
                continue;
            }
            $localSnapshot->draftGrQuantity = $r["draft_gr_qty"];
            $localSnapshot->postedGrQuantity = $r["posted_gr_qty"];
            $localSnapshot->confirmedGrBalance = $r["confirmed_gr_balance"];
            $localSnapshot->openGrBalance = $r["open_gr_qty"];
            $localSnapshot->draftAPQuantity = $r["draft_ap_qty"];
            $localSnapshot->postedAPQuantity = $r["posted_ap_qty"];
            $localSnapshot->openAPQuantity = $r["open_ap_qty"];
            $localSnapshot->billedAmount = $r["billed_amount"];
            $localSnapshot->openAPAmount = $localSnapshot->netAmount - $localSnapshot->billedAmount;

            $resultList[] = $localSnapshot;
        }

        return $resultList;
    }
}