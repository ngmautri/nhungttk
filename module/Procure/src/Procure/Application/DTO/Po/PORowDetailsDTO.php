<?php
namespace Procure\Application\DTO\Po;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowDetailDTO extends PORowDTO
{

    public $draftGrQuantity;
    
    public $postedGrQuantity;
    
    public $confirmedGrBalance;
    
    public $openGrBalance;
    
    public $draftAPQuantity;
    
    public $postedAPQuantity;
    
    public $openAPQuantity;
    
    public $billedAmount;
}
