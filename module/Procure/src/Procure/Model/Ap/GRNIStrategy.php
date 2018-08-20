<?php
namespace Procure\Model\Ap;

/**
 *
 * GOOD RECEIPT - INVOICE NOT RECEIPT YET
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRNIStrategy extends AbstractAPRowPostingStrategy
{

    public function doPosting($entity, $r, $u = null)
    {
        if (! $entity instanceof \Application\Entity\FinVendorInvoice) {
            throw new \Exception("Invalid Argument! Invoice is not found.");
        }

        if (! $r instanceof \Application\Entity\FinVendorInvoiceRow) {
            throw new \Exception("Invalid Argument! Invoice row is not found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

       // do nothing.
    }
}