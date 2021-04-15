<?php
namespace Application\Application\Service\Department\Tree;

use Application\Application\Command\Options\CmdOptions;
use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\Department\AbstractDepartmentTree;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Event\Company\DepartmentInserted;
use Application\Domain\Event\Company\DepartmentUpdated;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\Filter\CompanyQuerySqlFilter;
use Doctrine\ORM\EntityManager;
use Webmozart\Assert\Assert;

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

    public function insertNode(AbstractBaseNode $node, AbstractBaseNode $parent, CmdOptions $options = null)
    {
        Assert::isInstanceOf($node, AbstractBaseNode::class);
        Assert::isInstanceOf($parent, AbstractBaseNode::class);

        $parent->add($node);
        $target = $node;
        $defaultParams = new DefaultParameter();
        $params = [
            'options' => $options
        ];
        $event = new DepartmentInserted($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
    }

    public function updateNode(AbstractBaseNode $node)
    {
        $target = $node;
        $defaultParams = new DefaultParameter();
        $params = null;
        $event = new DepartmentUpdated($target, $defaultParams, $params);
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