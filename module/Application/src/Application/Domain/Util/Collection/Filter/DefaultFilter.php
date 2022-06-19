<?php
namespace Application\Domain\Util\Collection\Filter;

use Application\Domain\Util\Collection\Contracts\FilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultFilter implements FilterInterface
{

    private $offset;

    private $limit;

    public function __construct($offset = null, $limit = null)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getLimit()
    {
        return null;
    }

    public function getOffset()
    {
        return null;
    }
}
