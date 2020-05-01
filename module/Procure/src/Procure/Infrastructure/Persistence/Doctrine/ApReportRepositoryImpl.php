<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Procure\Infrastructure\Contract\SqlFilterInterface;
use Procure\Infrastructure\Mapper\ApMapper;
use Procure\Infrastructure\Persistence\ApReportRepositoryInterface;
use Procure\Infrastructure\Persistence\Helper\ApReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApReportRepositoryImpl extends AbstractDoctrineRepository implements ApReportRepositoryInterface
{

    public function getOfItem($item_id, $item_token)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\ApReportRepositoryInterface::getList()
     */
    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $results = ApReportHelper::getList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\FinVendorInvoice $doctrineRootEntity ;*/
            $doctrineRootEntity = $r[0];

            $rootSnapshot = ApMapper::createSnapshot($this->doctrineEM, $doctrineRootEntity);

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
     * @see \Procure\Infrastructure\Persistence\ApReportRepositoryInterface::getListWithCustomDTO()
     */
    public function getListWithCustomDTO(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\ApReportRepositoryInterface::getListTotal()
     */
    public function getListTotal(SqlFilterInterface $filter)
    {
        return ApReportHelper::getListTotal($this->getDoctrineEM(), $filter);
    }

    public function getAllRowWithCustomDTO(SqlFilterInterface $filter)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\ApReportRepositoryInterface::getAllRow()
     */
    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $results = ApReportHelper::getAllRow($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = array();
        foreach ($results as $r) {

            /**@var \Application\Entity\FinVendorInvoiceRow $entity ;*/
            $entity = $r;

            $snapShot = ApMapper::createRowSnapshot($this->getDoctrineEM(), $entity);

            if ($snapShot == null) {
                continue;
            }
            $resultList[] = $snapShot;
        }

        return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\ApReportRepositoryInterface::getAllRowTotal()
     */
    public function getAllRowTotal(SqlFilterInterface $filter)
    {
        return ApReportHelper::getAllRowTotal($this->getDoctrineEM(), $filter);
    }
}
