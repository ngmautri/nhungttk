<?php
namespace Procure\Application\Service\Output;

use Procure\Domain\GenericDoc;

/**
 * Director in builder pattern
 * SaveAs Interface
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface SaveAsInterface
{

    public function saveDocAs(GenericDoc $doc, AbstractRowFormatter $formatter);

    public function saveMultiplyRowsAs($rows, AbstractRowFormatter $formatter);
}
