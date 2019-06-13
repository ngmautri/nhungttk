<?php
namespace Procure\Infrastructure\Persistence;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Infrastructure\Persistence\SQL\PrSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePRListRepository extends AbstractDoctrineRepository implements PRListRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\PRListRepositoryInterface::getAllPrRow()
     */
    public function getAllPrRow($is_active = 1, $pr_year = 0, $balance = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = PrSQL::PR_ROW_SQL;
        $sql_tmp = '';

        $sql = sprintf($sql, $sql_tmp, $sql_tmp, $sql_tmp, $sql_tmp, $sql_tmp, $sql_tmp);
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

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
        
    }


}
