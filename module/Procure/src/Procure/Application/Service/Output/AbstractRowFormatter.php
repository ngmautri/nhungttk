<?php
namespace Procure\Application\Service\Output;

use Procure\Domain\RowSnapshot;

/**
 * Row Output
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractRowFormatter
{
    abstract public function format(RowSnapshot $row);
}
