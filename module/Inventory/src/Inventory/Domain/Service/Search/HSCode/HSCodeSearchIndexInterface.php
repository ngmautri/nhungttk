<?php
namespace Inventory\Domain\Service\Search\HSCode;

/**
 * HSCode Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface HSCodeSearchIndexInterface
{

    public function optimizeIndex();

    public function createIndex($rows);
}
