<?php
namespace Procure\Domain\Service\Search;

use Application\Application\Service\Search\Contracts\QueryFilterInterface;

/**
 * Purchase Request Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PrSearchQueryInterface
{

    public function search($q, QueryFilterInterface $filter = null);
}
