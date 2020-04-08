<?php
namespace Procure\Application\Service\Output;

use Procure\Domain\GenericDoc;

/**
 * Row Output Decorator
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface RowOutputInterface 
{
    public function createOutput(GenericDoc $doc);
    
    public function formatMultiplyRows($rows);
}
