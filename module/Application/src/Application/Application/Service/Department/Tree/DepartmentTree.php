<?php
namespace Application\Application\Service\Department\Tree;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\Department\AbstractDepartmentTree;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Event\Company\DepartmentInserted;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\Filter\CompanyQuerySqlFilter;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class DepartmentTree extends AbstractDepartmentTree
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::initTree()
     */
    public function initTree()
    {
        $rep = new CompanyQueryRepositoryImpl($this->getDoctrineEM());
        $filter = new CompanyQuerySqlFilter();
        $results = $rep->getDepartmentList($filter);

        foreach ($results as $row) {

            /** @var DepartmentSnapshot $row ; */

            $id = $row->getNodeId();
            $parent_id = $row->getNodeParentId();

            // convert to Generic Component
            $genericComponent = new DepartmentNode();
            $genericComponent->setId($row->getNodeId());
            $genericComponent->setParentId($row->getNodeParentId());
            $genericComponent->setNodeName($row->getDepartmentName());
            $genericComponent->setNodeCode($row->getDepartmentCode());
            $genericComponent->setNodeDescription($row->getRemarks());

            $this->data[$id] = $genericComponent;
            $this->index[$parent_id][] = $id;
        }
        return $this;
    }

    public function insertNode(AbstractBaseNode $node, AbstractBaseNode $parent)
    {
        $parent->add($node);
        $target = $node;
        $defaultParams = new DefaultParameter();
        $params = null;
        $event = new DepartmentInserted($target, $defaultParams, $params);
        $this->addEvent($event);
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