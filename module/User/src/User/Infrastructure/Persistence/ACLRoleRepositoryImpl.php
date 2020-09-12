<?php
namespace User\Infrastructure\Persistence\Doctrine;

use Application\Entity\NmtApplicationAclRole;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use User\Infrastructure\Persistence\Contracts\ACLRoleRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ACLRoleRepositoryImpl extends AbstractDoctrineRepository implements ACLRoleRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \User\Infrastructure\Persistence\Contracts\ACLRoleRepositoryInterface::getRole()
     */
    public function getRole($roleId)
    {
        $sql = "select
*
from nmt_application_acl_role
where id=" . $roleId;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());

            /**
             *
             * @var NmtApplicationAclRole
             */
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtApplicationAclRole', 'nmt_application_acl_role');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \User\Infrastructure\Persistence\Contracts\ACLRoleRepositoryInterface::getRoot()
     */
    public function getRoot()
    {
        $sql = "select
*
from nmt_application_acl_role
where 1";

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtApplicationAclRole', 'nmt_application_acl_role');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
