<?php
namespace Procure\Model\Ap;

/**
 * 
 * GOOD RECEIPT - INVOICE NOT RECEIPT YET
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRNI_Strategy extends AbstractAPRowPostingStrategy
{
    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Model\Ap\AbstractAPRowPostingStrategy::doPosting()
     */
    public function doPosting($ap, $row, $user = null)
    {
        return null;
    }
}