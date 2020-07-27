<?php
namespace Inventory\Application\Export\Item\Contracts;

use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;

/**
 * Director in builder pattern
 * SaveAs Interface
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface SaveAsInterface
{

    public function saveAs($rows, AbstractRowFormatter $formatter);
}
