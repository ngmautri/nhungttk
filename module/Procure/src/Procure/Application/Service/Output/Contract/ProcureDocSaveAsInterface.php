<?php
namespace Procure\Application\Service\Output\Contract;

use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;

/**
 * Director in builder pattern
 * SaveAs Interface.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface ProcureDocSaveAsInterface
{

    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter, $offset = null, $limit = null);
}
