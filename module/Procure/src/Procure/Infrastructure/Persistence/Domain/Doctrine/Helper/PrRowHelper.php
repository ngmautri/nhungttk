<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Helper;
use Doctrine\ORM\EntityManager;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\PrRowSQL;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrRowHelper
{
    public static function getRows(EntityManager $doctrineEM, PrRowReportSqlFilter $filter)
    {
        
        if (! $filter instanceof PrRowReportSqlFilter) {
            return null;
        }
        
        $tmp1 = '';
        
        if($filter->getPrId()>0){
            $tmp1 .= sprintf(" AND nmt_procure_pr.pr_id=%s", $filter->getPrId());
        }
        if($filter->getItemId()>0){
            $tmp1 .=sprintf(" AND nmt_procure_pr_row.item_id=%s", $filter->getItemId());
        }
        
        $sql = PrRowSQL::PR_ROW_SQL;
        $sql1 = sprintf(PrRowSQL::PR_QO_SQL, $tmp1);
        $sql2 = sprintf(PrRowSQL::PR_PO_SQL, $tmp1);
        $sql3 = sprintf(PrRowSQL::PR_POGR_SQL, $tmp1);
        $sql4 = sprintf(PrRowSQL::PR_AP_SQL, $tmp1);
        $sql4 = sprintf(PrRowSQL::PR_STOCK_GR_SQL_SQL, $tmp1);
        $sql5 = sprintf(PrRowSQL::ITEM_LAST_AP_SQL, $tmp1);
        
        $sql = sprintf($sql, $sql1, $sql2, $sql3, $sql4, $sql5, $id);
        
        
    }
       
}
