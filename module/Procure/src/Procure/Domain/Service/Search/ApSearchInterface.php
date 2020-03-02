<?php
namespace Procure\Domain\Service\Search;

/**
 * AP Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ApSearchInterface
{

    public function search($q);

    public function createIndex();

    public function optimizeIndex();

    public function createDoc($doc, $isNew = true);

    public function updateIndex($entityId, $isNew = true, $optimized = false);
}
