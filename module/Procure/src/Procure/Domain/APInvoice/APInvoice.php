<?php
namespace Procure\Domain\APInvoice;

use Application\Notification;
use Procure\Domain\Service\APInvoicePostingService;
use Procure\Domain\Service\APInvoiceSpecificationService;
use Procure\Domain\Event\GRIRPostedEvent;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoice extends GenericAPInvoice
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Domain\APInvoice\GenericAPInvoice::afterPost()
     */
    protected function afterPost(APInvoiceSpecificationService $specificationService, APInvoicePostingService $postingService, Notification $notification = null)
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Domain\APInvoice\GenericAPInvoice::prePost()
     */
    protected function prePost(APInvoiceSpecificationService $specificationService, APInvoicePostingService $postingService, Notification $notification = null)
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Domain\APInvoice\GenericAPInvoice::raiseEvent()
     */
    protected function raiseEvent()
    {
        $this->registerEvent(new GRIRPostedEvent($this));
    }

    protected function doPost(APInvoiceSpecificationService $specificationService, APInvoicePostingService $postingService, Notification $notification = null)
    {}
}