<?php
namespace Inventory\Application\Service\ItemCatalog\Tree;

use Application\Domain\Util\Tree\Node\AbstractNode;
use Application\Domain\Util\Tree\Node\GenericNode;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class ItemCatalogNode extends GenericNode
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

        return $this->getNodeName() == $other->getNodeName();
    }
}