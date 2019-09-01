<?php
namespace Procure\Application\Service\PO\Output;


/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class PoRowOutputStrategy
{

    const OUTPUT_IN_ARRAY = "1";

    const OUTPUT_IN_EXCEL = "2";

    const OUTPUT_IN_HMTL_TABLE = "3";

    const OUTPUT_IN_OPEN_OFFICE = "4";

    abstract public function createOutput($result);

   
  
}
