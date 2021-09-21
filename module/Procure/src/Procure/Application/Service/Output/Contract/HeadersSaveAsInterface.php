<?php
namespace Procure\Application\Service\Output\Contract;

use Procure\Application\Service\Output\Formatter\Header\AbstractHeaderFormatter;

/**
 *
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
