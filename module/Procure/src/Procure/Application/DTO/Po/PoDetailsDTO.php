<?php
namespace Procure\Application\DTO\Po;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoDetailsDTO extends PoDTO
{

    public $totalRows;

    public $totalActiveRows;

    public $maxRowNumber;

    public $netAmount;

    public $taxAmount;

    public $grossAmount;

    public $discountAmount;
    
    public $billedAmount;
    
    public $completedRows;
}
