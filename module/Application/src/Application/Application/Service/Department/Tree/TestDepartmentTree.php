<?php
namespace Application\Application\Service\Department\Tree;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\Contracts\DefaultDepartment;
use Application\Domain\Company\Department\AbstractDepartmentTree;
use Application\Domain\Event\Company\DepartmentInserted;
use Application\Domain\Util\Tree\Node\AbstractBaseNode;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class TestDepartmentTree extends AbstractDepartmentTree
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::initTree()
     */
    public function initTree()
    {
        $genericNode = new DepartmentNode();
        $genericNode->setId(DefaultDepartment::ROOT);
        $genericNode->setNodeName(DefaultDepartment::ROOT);

        $this->data[DefaultDepartment::ROOT] = $genericNode;

        $genericNode = new DepartmentNode();
        $genericNode->setId("Finance");
        $genericNode->setParentId(DefaultDepartment::ROOT);
        $genericNode->setNodeName('Finance');
        $this->data["Finance"] = $genericNode;

        $genericNode = new DepartmentNode();
        $genericNode->setId("Accounting");
        $genericNode->setParentId("Finance");
        $genericNode->setNodeName('Accounting');
        $this->data["Accounting"] = $genericNode;

        $genericNode = new DepartmentNode();
        $genericNode->setId("Controlling");
        $genericNode->setParentId("Finance");
        $genericNode->setNodeName('Controlling');
        $this->data["Controlling"] = $genericNode;

        $genericNode = new DepartmentNode();
        $genericNode->setId("HR & ADM");
        $genericNode->setParentId(DefaultDepartment::ROOT);
        $genericNode->setNodeName('HR & ADM');
        $this->data["HR & ADM"] = $genericNode;

        $this->index[DefaultDepartment::ROOT][] = "Finance";
        $this->index[DefaultDepartment::ROOT][] = "HR & ADM";
        $this->index['Finance'][] = "Accounting";
        $this->index['Finance'][] = "Controlling";
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::insertNode()
     */
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
}