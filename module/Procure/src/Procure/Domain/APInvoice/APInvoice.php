<?php
namespace Procure\Domain\APInvoice;

use Application\Notification;
use Procure\Domain\Service\APInvoicePostingService;
use Procure\Domain\Service\APInvoiceSpecificationService;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoice extends GenericAPInvoice
{
    
    protected function afterPost(APInvoiceSpecificationService $specificationService, APInvoicePostingService $postingService, Notification $notification = null)
    {}

    protected function prePost(APInvoiceSpecificationService $specificationService, APInvoicePostingService $postingService, Notification $notification = null)
    {}

    protected function raiseEvent()
    {}

    protected function doPost(APInvoiceSpecificationService $specificationService, APInvoicePostingService $postingService, Notification $notification = null)
    {}
}