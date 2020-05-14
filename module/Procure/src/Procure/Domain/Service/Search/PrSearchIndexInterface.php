<?php
namespace Procure\Domain\Service\Search;

use Procure\Domain\DocSnapshot;

/**
 * Purchase Request Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PrSearchIndexInterface
{

    public function createDoc(DocSnapshot $doc);

    public function optimizeIndex();

    public function createIndex($rows);
}
