<?php
namespace Procure\Domain\Service\Search;

/**
 * Purchase Request Search
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PrSearchInterface
{

    public function search($q);

    public function createIndex();

    public function optimizeIndex();

    public function createDoc($doc, $isNew = true);

    public function updateIndex($entityId, $isNew = true, $optimized = false);
}
