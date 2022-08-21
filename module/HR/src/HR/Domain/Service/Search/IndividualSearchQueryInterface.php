<?php
namespace HR\Domain\Service\Search;

use Application\Application\Service\Search\Contracts\QueryFilterInterface;

/**
 * Invididual Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface IndividualSearchQueryInterface
{

    public function search($q, QueryFilterInterface $filter = null);

    public function queryForAutoCompletion($q, QueryFilterInterface $filter, $maxHit = 10, $returnDetail = true);
}
