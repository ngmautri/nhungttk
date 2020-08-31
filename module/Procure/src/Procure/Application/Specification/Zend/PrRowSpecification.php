<?php
namespace Procure\Application\Specification\Zend;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $warehouseId = null;
        if (isset($subject["warehouseId"])) {
            $warehouseId = $subject["warehouseId"];
        }

        $prRowId = null;
        if (isset($subject["prRowId"])) {
            $prRowId = $subject["prRowId"];
        }

        if ($this->doctrineEM == null || $warehouseId == null || $prRowId == null) {
            return false;
        }

        $sql_tmp = "
SELECT * FROM nmt_procure_pr_row
where nmt_procure_pr_row.id = %s and nmt_procure_pr_row.warehouse_id=%s";

        $sql = sprintf($sql_tmp, $prRowId, $warehouseId);
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();

            if ($result == null) {
                return false;
            }

            return true;
        } catch (NoResultException $e) {
            return false;
        }
    }
}