<?php
namespace Procure\Application\Service\Output;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractSpreadsheetBuilder implements SpreadsheetBuilderInterface
{

    protected $phpSpreadsheet;

    public function __construct()
    {
        $this->phpSpreadsheet = new Spreadsheet();
    }

    /**
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function getPhpSpreadsheet()
    {
        return $this->phpSpreadsheet;
    }
}
