<?php
namespace Procure\Domain\PurchaseOrder;

use Procure\Domain\APInvoice\Factory\APFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericPO extends AbstractPO
{
    

   
    public function makeAPInvoice($this)
    {
        return APFactory::createAPInvoiceFromPO($this);
    }
}