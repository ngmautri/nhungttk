<?php
namespace Inventory\Domain\Service\Search\HSCode;

use Inventory\Domain\HSCode\BaseHSCodeSnapshot;

/**
 * HSCode Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface HSCodeSearchIndexInterface
{

    public function createDoc(BaseHSCodeSnapshot $doc);

    public function optimizeIndex();

    public function createIndex($rows);
}
