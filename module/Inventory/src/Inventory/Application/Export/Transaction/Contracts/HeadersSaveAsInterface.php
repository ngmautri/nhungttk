<?php
namespace Inventory\Application\Export\Transaction\Contracts;

use Inventory\Application\Export\Transaction\Formatter\Header\AbstractHeaderFormatter;

/**
 * Director in builder pattern
 * SaveAs Interface
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface HeadersSaveAsInterface
{

    public function saveAs($headers, AbstractHeaderFormatter $formatter);
}
