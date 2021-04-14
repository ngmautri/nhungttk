<?php
namespace Application\Application\Service\Department\Tree;

use Application\Domain\Util\Tree\Node\AbstractNode;
use Application\Domain\Util\Tree\Node\GenericNode;

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

        // $t = $this->getNodeName() == $other->getNodeName();
        // echo \sprintf("[%s equals %s ? => %s] \n", $this->getNodeName(), $other->getNodeName(), $t);

        return $this->getNodeName() == $other->getNodeName();
    }
}