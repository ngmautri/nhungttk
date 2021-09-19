<?php
namespace Procure\Infrastructure\Persistence;

/**
 *
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ProcureReportRepositoryInterface
{

    public function getPriceOfItem($itemId, $itemToken);
}
