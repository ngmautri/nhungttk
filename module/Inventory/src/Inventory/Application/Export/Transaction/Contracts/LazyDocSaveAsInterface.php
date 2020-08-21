<?php
namespace Inventory\Application\Export\Transaction\Contracts;

use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;
use Inventory\Domain\Transaction\GenericTrx;

/**
 * Director in builder pattern
 * SaveAs Interface.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface LazyDocSaveAsInterface
{

    public function saveAs(GenericTrx $doc, AbstractRowFormatter $formatter, $offset = null, $limit = null);
}
