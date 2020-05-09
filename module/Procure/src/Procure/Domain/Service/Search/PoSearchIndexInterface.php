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

    public function createIndex();

    public function optimizeIndex();

    public function createDoc(DocSnapshot $doc);

    public function updateDoc(DocSnapshot $doc);

    public function updateIndex($entityId, $isNew = true, $optimized = false);
}
