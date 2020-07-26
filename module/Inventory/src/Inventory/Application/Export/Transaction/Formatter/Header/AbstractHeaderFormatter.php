<?php
namespace Inventory\Application\Export\Transaction\Formatter\Header;

use Procure\Domain\DocSnapshot;

/**
 * Row Output
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractHeaderFormatter
{

    abstract public function format(DocSnapshot $header);
}
