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
}
