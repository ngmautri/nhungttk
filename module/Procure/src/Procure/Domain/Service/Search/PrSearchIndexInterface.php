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

    /**
     * create new index.
     * it should run in background
     *
     * @param array $rows
     */
    public function createIndex($rows);

    /**
     * indexing new dow
     *
     * @param DocSnapshot $doc
     */
    public function createDoc(DocSnapshot $doc);

    /**
     *
     * @param array $rows
     */
    public function updateIndexRow($rows);

    /**
     * Optimize index.
     * it should run in background
     */
    public function optimizeIndex();
}
