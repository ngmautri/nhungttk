<?php
namespace Application\Domain\Util\Composite;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Leaf extends AbstractBaseComponent
{

    public function getNumberOfChildren()
    {
        return 1;
    }
}
