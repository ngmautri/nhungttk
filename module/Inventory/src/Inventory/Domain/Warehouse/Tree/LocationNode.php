<?php
namespace Inventory\Domain\Warehouse\Tree;

use Application\Domain\Util\Tree\Node\AbstractNode;
use Application\Domain\Util\Tree\Node\GenericNode;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class LocationNode extends GenericNode
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

        return \strtolower(trim($this->getNodeCode())) === \strtolower(trim($other->getNodeCode()));
    }

    public function setNodeCode($nodeCode)
    {
        Assert::notNull($nodeCode, 'Location Code empty!');
        $this->nodeCode = trim($nodeCode);
    }
}