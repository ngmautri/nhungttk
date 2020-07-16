<?php
namespace Inventory\Domain\Service\Search\HSCode;

use Application\Application\Service\Search\Contracts\QueryFilterInterface;

/**
 * Purchase Order Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface HSCodeSearchQueryInterface
{

    public function search($q, QueryFilterInterface $filter = null);
}
