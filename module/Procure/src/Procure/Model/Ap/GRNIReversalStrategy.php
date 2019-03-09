<?php
namespace Procure\Model\Ap;

/**
 *
 * GOOD RECEIPT - INVOICE NOT RECEIPT YET
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRNIReservalStrategy extends AbstractAPRowPostingStrategy
{

    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Model\Ap\AbstractAPRowPostingStrategy::doPosting()
     */
    public function doPosting($entity, $r, $u = null, $isPosting=1)
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