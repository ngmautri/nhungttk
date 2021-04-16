<?php
namespace Application\Application\Service\Department\Tree;

use Application\Domain\Util\Tree\Node\AbstractNode;
use Application\Domain\Util\Tree\Node\GenericNode;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class DepartmentNode extends GenericNode
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\Node\GenericNode::equals()
     */
    public function equals(AbstractNode $other = null)
    {
        if ($other == null) {
            return false;
        }

        return \strtolower(trim($this->getNodeName())) == \strtolower(trim($other->getNodeName()));
    }

    public function setNodeName($nodeName)
    {
        Assert::notNull($nodeName, 'Department empty!');
        $this->nodeName = trim($nodeName);
    }
}