<?php
namespace Inventory\Application\Export\Transaction\Contracts;

use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;

/**
 * Director in builder pattern
 * SaveAs Interface.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface DocSaveAsInterface
{

    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter);
}
