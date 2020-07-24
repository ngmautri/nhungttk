<?php
namespace Application\Domain\Util\Tree\Node;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface NodeInterface
{

    public function isLeaf();

    public function getChildCount();

    public function equals(AbstractNode $other = null);
}
