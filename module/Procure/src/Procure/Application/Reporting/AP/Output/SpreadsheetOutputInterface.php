<?php
namespace Procure\Application\Reporting\AP\Output;

use PhpOffice\PhpSpreadsheet\Spreadsheet;


/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface SpreadsheetOutputInterface
{

    protected function createHeader(Spreadsheet $objPHPExcel);
    
    protected function createFooter(Spreadsheet $objPHPExcel);    
  
}
