<?php
namespace Application\Domain\Company\AccountChart\Tree;

use Application\Domain\Util\Tree\Node\AbstractNode;
use Application\Domain\Util\Tree\Node\GenericNode;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class AccountChartNode extends GenericNode
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
        Assert::notNull($nodeCode, 'AccountChartNode empty!');
        $this->nodeCode = trim($nodeCode);
    }
}