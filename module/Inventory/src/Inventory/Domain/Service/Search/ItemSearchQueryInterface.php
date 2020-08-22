<?php
namespace Inventory\Domain\Service\Search;

use Application\Application\Service\Search\Contracts\QueryFilterInterface;

/**
 * Purchase Order Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemSearchQueryInterface
{

    public function search($q, QueryFilterInterface $filter = null);

    public function queryForAutoCompletion($q, QueryFilterInterface $filter, $maxHit = 10, $returnDetail = true);
}
