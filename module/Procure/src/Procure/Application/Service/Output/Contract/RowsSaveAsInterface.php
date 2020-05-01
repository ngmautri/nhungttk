<?php
namespace Procure\Application\Service\Output\Contract;

use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;

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
