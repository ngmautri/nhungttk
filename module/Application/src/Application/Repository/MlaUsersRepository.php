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
     * @param number $transition_id
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function getOtherAgentOfWfTransition($transition_id, $limit = 0, $offset = 0)
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

    /**
     *
     * @param int $user_id
     * @return array
     */
    public function getRoleByUserId($user_id)
    {
        $sql = " select
				nmt_application_acl_user_role. *,
				nmt_application_acl_role.role as role,
				nmt_application_acl_role.parent_id,
				nmt_application_acl_role.path
		from nmt_application_acl_user_role
		join nmt_application_acl_role
		on nmt_application_acl_role.id = nmt_application_acl_user_role.role_id
        where 1 AND nmt_application_acl_user_role.user_id = " . $user_id;

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @param \Application\Entity\MlaUsers $user
     * @return boolean
     */
    public function isAdministrator($user)
    {

        // Get of User 1
        $criteria = array(
            'user' => $user
        );

        $roles = $this->_em->getRepository('\Application\Entity\NmtApplicationAclUserRole')->findBy($criteria);

        if (count($roles) > 0) {
            foreach ($roles as $r) {

                /**@var \Application\Entity\NmtApplicationAclUserRole $r ;*/
                if ($r->getRole()->getRole() == \Application\Model\Constants::USER_ROLE_ADMINISTRATOR) {
                    return true;
                }
            }
        }
        return false;
    }
}

