<?php
namespace Application\Domain\Util\Collection\Filter;

use Application\Domain\Util\Collection\Contracts\FilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NullFilter implements FilterInterface
{

    public function getLimit()
    {
        return null;
    }

    public function getOffset()
    {
        return null;
    }
}
