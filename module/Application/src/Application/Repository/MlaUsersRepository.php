<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 *
 * @author nmt
 *        
 */
class MlaUsersRepository extends EntityRepository
{
    
     /** @var \Application\Entity\MlaUsers $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\MlaUsersRepository")

    private $sql = "";
    
   /**
    * 
    * @param unknown $transition_id
    * @param unknown $limit
    * @param unknown $offset
    * @return array
    */
    public function getOtherAgentOfWfTransition($transition_id, $limit=null, $offset=null)
    {
        $sql_AgentOf = "
            SELECT nmt_wf_transition_agent.agent_id FROM nmt_wf_transition_agent
            WHERE nmt_wf_transition_agent.transition_id = " . $transition_id;
        
        $sql = "
		SELECT *
        FROM mla_users
        WHERE mla_users.id NOT IN
        		(" . $sql_AgentOf . ")";
        
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

