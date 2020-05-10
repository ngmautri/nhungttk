<?php
namespace Procure\Domain\Service\Search;

use Procure\Domain\DocSnapshot;

/**
 * Purchase Order Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PoSearchIndexInterface
{

    public function createDoc(DocSnapshot $doc);

    public function optimizeIndex();

    public function createIndex($rows);
}
