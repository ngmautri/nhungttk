<?php
namespace Application\Domain\Util\Tree\Output;

use Application\Domain\Util\Tree\Node\AbstractBaseNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractFormatter
{

    abstract public function format(AbstractBaseNode $node, $level = 0);
}
