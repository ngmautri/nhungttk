<?php
namespace Application\Application\Service\Document\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Application\Application\Service\Document\DocumentBuilderInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractBuilder implements DocumentBuilderInterface
{

    protected $phpSpreadSheet;

  
    public function __construct()
    {
        $this->phpSpreadSheet = new Spreadsheet();
    }
    
    /**
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function getPhpSpreadSheet()
    {
        return $this->phpSpreadSheet;
    }
    
}
