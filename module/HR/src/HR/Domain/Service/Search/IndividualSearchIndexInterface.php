<?php
namespace HR\Domain\Service\Search;

use HR\Domain\Employee\IndividualSnapshot;

/**
 * Indivudial Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface IndividualSearchIndexInterface
{

    public function createDoc(IndividualSnapshot $snapshot);

    public function optimizeIndex();

    public function createIndex($rows);
}
