<?php
namespace User\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use User\Domain\User\Factory\UserFactory;
use User\Infrastructure\Mapper\UserMapper;
use User\Infrastructure\Persistence\Contracts\UserReportRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserReportRepositoryImpl extends AbstractDoctrineRepository implements UserReportRepositoryInterface
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
    {
        if ($idList == null) {
            return null;
        }

        $inString = '';
        $n = 0;

        foreach ($idList as $id) {
            $n ++;

            if ($n == 1) {
                $inString = $id;
            } else {
                $inString = $inString . ',' . $id;
            }
        }

        $f = "Select * from mla_users";
        $f = $f . " WHERE 1 AND mla_users.id IN(%s)";
        $sql = \sprintf($f, $inString);
        $users = [];
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\MlaUsers', 'mla_users');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $results = $query->getResult();

            if ($results == null) {
                return null;
            }

            foreach ($results as $entity) {

                $snapshot = UserMapper::createSnapshot($this->doctrineEM, $entity, false);
                $users[] = [
                    $snapshot->id => UserFactory::contructFromDB($snapshot)
                ];
            }

            return $users;
        } catch (NoResultException $e) {
            return;
        }
    }
}
