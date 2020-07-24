<?php
namespace Application\Domain\Util\Tree\Node;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericNode extends AbstractBaseNode
{

    public function __construct()
    {
        $this->children = new \SplObjectStorage();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\Node\NodeInterface::equals()
     */
    public function equals(AbstractNode $other = null)
    {
        return $other == $this;
    }

    public function __toString()
    {
        return $this->getNodeName();
    }
}
