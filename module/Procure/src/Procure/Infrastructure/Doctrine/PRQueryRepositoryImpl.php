<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Procure\Domain\PurchaseOrder\Repository\PrQueryRepositoryInterface;
/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRQueryRepositoryImpl  extends AbstractDoctrineRepository implements PrQueryRepositoryInterface
{
    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Domain\PurchaseOrder\Repository\PrQueryRepositoryInterface::getHeaderIdByRowId()
     */
    public function getHeaderIdByRowId($id)
    {
        $sql = "
SELECT
nmt_procure_pr_row.pr_id AS prId
FROM nmt_procure_pr_row
WHERE id = %s";
        
        $sql = sprintf($sql, $id);
        
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $rsm->addScalarResult("prId", "prId");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getSingleResult()["prId"];
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    public function getVersion($id, $token = null)
    {}

   
    public function getVersionArray($id, $token = null)
    {}
}
