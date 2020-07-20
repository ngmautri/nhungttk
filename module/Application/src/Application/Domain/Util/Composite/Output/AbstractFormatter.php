<?php
namespace Application\Domain\Util\Composite\Output;

use Application\Domain\Util\Composite\AbstractBaseComponent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractFormatter
{

    abstract public function format(AbstractBaseComponent $component, $level = 0);
}
