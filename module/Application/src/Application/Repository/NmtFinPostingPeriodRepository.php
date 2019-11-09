<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class NmtFinPostingPeriodRepository extends EntityRepository
{

    /** @var \Application\Entity\NmtFinPostingPeriod $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\NmtFinPostingPeriodRepository")
    private $sql = "";

    /**
     *
     * @param \Datetime $date
     * @return NULL
     */
    public function getPostingPeriod(\Datetime $date)
    {
        $query = $this->_em->createQuery('SELECT p
        FROM Application\Entity\NmtFinPostingPeriod p
        WHERE p.postingFromDate <= :date AND p.postingToDate >= :date')->setParameter('date', $date);
        $result = $query->getResult();
        
        if (count($result) == 1) {
            return $result[0];
        }
        return null;
    }

    /**
     *
     * @param \Datetime $date
     * @return mixed|NULL
     */
    public function getPostingPeriodStatus(\Datetime $date)
    {
        $query = $this->_em->createQuery('SELECT p
        FROM Application\Entity\NmtFinPostingPeriod p
        WHERE p.postingFromDate <= :date AND p.postingToDate >= :date')->setParameter('date', $date);
        $result = $query->getResult();
        
        if (count($result) == 1) {
            return $result[0]->getPeriodStatus();
        }
        return "Period not found";
    }

     /**
      * 
      *  @param int $source_id
      *  @param int $target_id
      *  @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL|NULL
      */
    public function getLatestFX($source_id, $target_id)
    {
        $sql = "
SELECT
*
FROM fin_fx 

JOIN 
(
    SELECT
    MAX(fin_fx.fx_date) AS fx_date,
    fin_fx.source_currency
    FROM fin_fx
    GROUP BY fin_fx.source_currency
)
AS  fin_fx1
ON fin_fx1.fx_date = fin_fx.fx_date AND fin_fx.source_currency = fin_fx1.source_currency
WHERE 1
";
        
        $sql = $sql . sprintf(" AND fin_fx.source_currency=%s and fin_fx.target_currency=%s", $source_id, $target_id);
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinFx', 'fix_fx');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            var_dump($e->getMessage());
            
            return null;
        }
    }
}

