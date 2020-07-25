<?php
namespace Application\Domain\Util\Tree\Repository;

use Application\Domain\Util\Tree\Node\AbstractNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface TreeQueryRepositoryInterface
{

    public function store(AbstractNode $node);
}