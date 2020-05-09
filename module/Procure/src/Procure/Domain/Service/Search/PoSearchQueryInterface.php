<?php
namespace Procure\Domain\Service\Search;

/**
 * Purchase Order Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PoSearchQueryInterface
{

    public function search($q);
}
