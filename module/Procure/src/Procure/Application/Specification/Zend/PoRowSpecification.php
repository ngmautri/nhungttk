<?php
namespace Procure\Application\Specification\Zend;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $vendorId = null;
        if (isset($subject["vendorId"])) {
            $vendorId = $subject["vendorId"];
        }

        $poRowId = null;
        if (isset($subject["poRowId"])) {
            $poRowId = $subject["poRowId"];
        }

        if ($this->doctrineEM == null || $vendorId == null || $poRowId == null) {
            return false;
        }

        $sql_tmp = "
SELECT nmt_procure_po_row.id, nmt_procure_po.vendor_id FROM nmt_procure_po_row
left join nmt_procure_po
on nmt_procure_po.id =nmt_procure_po_row.po_id
where nmt_procure_po_row.id = %s and nmt_procure_po.vendor_id=%s";

        $sql = sprintf($sql_tmp, $poRowId, $vendorId);
        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtFinPostingPeriod', 'nmt_fin_posting_period');
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