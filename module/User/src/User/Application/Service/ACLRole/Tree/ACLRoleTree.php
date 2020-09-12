<?php
namespace User\Application\Service\ACLRole\Tree;

use Application\Domain\Util\Tree\AbstractTree;
use Application\Entity\NmtApplicationAclRole;
use Doctrine\ORM\EntityManager;
use User\Infrastructure\Persistence\Doctrine\ACLRoleRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class ACLRoleTree extends AbstractTree
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::initTree()
     */
    public function initTree()
    {
        $rep = new ACLRoleRepositoryImpl($this->getDoctrineEM());
        $results = $rep->getRoot();
        foreach ($results as $row) {

            /** @var NmtApplicationAclRole $row ; */

            $id = $row->getId();
            $parent_id = $row->getParentId();

            // convert to Generic Component
            $genericComponent = new ACLRoleNode();
            $genericComponent->setId($row->getId());
            $genericComponent->setParentId($row->getParentId());
            $genericComponent->setNodeName($row->getRole());
            $genericComponent->setNodeCode($row->getRole());
            $genericComponent->setNodeDescription($row->getRemarks());
            $this->data[$id] = $genericComponent;
            $this->index[$parent_id][] = $id;
        }
        return $this;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \Application\Service\DepartmentService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}