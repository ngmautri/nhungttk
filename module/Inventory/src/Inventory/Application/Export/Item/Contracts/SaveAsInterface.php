<?php
namespace Inventory\Application\Export\Item\Contracts;

use Inventory\Application\Export\Item\Formatter\AbstractFormatter;

/**
 * Director in builder pattern
 * SaveAs Interface
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface SaveAsInterface
{

    public function saveAs($rows, AbstractFormatter $formatter);
}
