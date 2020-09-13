<?php
namespace User\Infrastructure\Persistence;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineUserRepository extends AbstractDoctrineRepository implements UserRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \User\Infrastructure\Persistence\UserRepositoryInterface::isAdministrator()
     */
    public function isAdministrator($userId)
    {
        // Get of User 1
        $criteria = array(
            'user' => $userId
        );

        if ($this->doctrineEM == null)
            return false;

        $roles = $this->doctrineEM->getRepository('\Application\Entity\NmtApplicationAclUserRole')->findBy($criteria);

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

    /**
     *
     * {@inheritdoc}
     * @see \User\Infrastructure\Persistence\UserRepositoryInterface::getRoleByUserId()
     */
    public function getRoleByUserId($userId)
    {
        if ($this->doctrineEM == null)
            return null;

        $sql = " select
				nmt_application_acl_user_role. *,
				nmt_application_acl_role.role as role,
				nmt_application_acl_role.parent_id,
				nmt_application_acl_role.path
		from nmt_application_acl_user_role
		join nmt_application_acl_role
		on nmt_application_acl_role.id = nmt_application_acl_user_role.role_id
        where 1 AND nmt_application_acl_user_role.user_id = " . $userId;

        $stmt = $this->doctrineEM->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * {@inheritdoc}
     * @see \User\Infrastructure\Persistence\UserRepositoryInterface::getOtherAgentOfWfTransition()
     */
    public function getOtherAgentOfWfTransition($transition_id, $limit = 0, $offset = 0)
    {
        if ($this->doctrineEM == null)
            return null;

        $sql_AgentOf = "
            SELECT nmt_wf_transition_agent.agent_id FROM nmt_wf_transition_agent
            WHERE nmt_wf_transition_agent.transition_id = " . $transition_id;

        $sql = "
		SELECT *
        FROM mla_users
        WHERE mla_users.id NOT IN
        		(" . $sql_AgentOf . ")";

        $stmt = $this->doctrineEM->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserList($idList)
    {}
}
