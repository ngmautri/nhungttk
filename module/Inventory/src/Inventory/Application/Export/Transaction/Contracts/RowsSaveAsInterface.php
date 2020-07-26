<?php
namespace Inventory\Application\Export\Transaction\Contracts;

use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;

/**
 * Director in builder pattern
 * SaveAs Interface
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface RowsSaveAsInterface
{

    public function saveAs($rows, AbstractRowFormatter $formatter);
}
