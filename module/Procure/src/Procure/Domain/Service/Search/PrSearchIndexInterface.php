<?php
namespace Procure\Domain\Service\Search;

/**
 * Purchase Request Search
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface PrSearchIndexInterface extends ProcureSearchIndexInterface
{

    /**
     *
     * @param array $rows
     */
    public function updateIndexRow($rows);
}
