<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
/**
 *
 * @author nmt
 *        
 */
class NmtFinPostingPeriodRepository extends EntityRepository
{

    /** @var \Application\Entity\NmtFinPostingPeriod $e*/    
    // @ORM\Entity(repositoryClass="Application\Repository\NmtFinPostingPeriodRepository")
    private $sql = "";

    
    /**
     * 
     */
    public function getPostingPeriod(\Datetime $date)
    {
      
        $query = $this->_em->createQuery('SELECT p
        FROM Application\Entity\NmtFinPostingPeriod p
        WHERE p.postingFromDate <= :date AND p.postingToDate >= :date')->setParameter('date', $date);
        $result = $query->getResult();
       
        if(count($result)==1){
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
        
        if(count($result)==1){
            return $result[0]->getPeriodStatus();
        }
        return "Period not found";
    }
}

