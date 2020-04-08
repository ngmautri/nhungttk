<?php
namespace Procure\Application\Service\Output;

use Procure\Domain\GenericRow;

/**
 * Row Output
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class RowOutputStrategy implements RowOutputInterface
{

    const OUTPUT_IN_ARRAY = "1";

    const OUTPUT_IN_EXCEL = "2";

    const OUTPUT_IN_HMTL_TABLE = "3";

    const OUTPUT_IN_OPEN_OFFICE = "4";

    abstract public function formatRow(GenericRow $row);
}
