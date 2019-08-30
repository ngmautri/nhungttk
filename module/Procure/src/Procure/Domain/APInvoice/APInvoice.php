<?php
namespace Procure\Domain\APInvoice;

use Application\Notification;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\APSpecificationService;
use Procure\Domain\APInvoice\Factory\APFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoice extends GenericAPDoc
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\APInvoice\AbstractAPDoc::specify()
     */
    public function specify()
    {
        $this->docType = APDocType::AP_INVOICE;
    }

    protected function afterPost(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {}

    protected function prePost(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {}

    protected function specificHeaderValidation(APSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {}

    protected function specificValidation(APSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {}

    protected function raiseEvent()
    {}

    protected function doPost(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {}

    protected function specificRowValidation(ApDocRow $row, APSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {
        // throw new \Exception("i like it");
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\APInvoice\GenericAPDoc::doReverse()
     */
    protected function doReverse(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {
        // create new reserval
        $rootEntity = APFactory::createAPDocument(APDocType::AP_INVOICE_REVERSAL);
        $rootEntity->makeSnapshot($this->makeSnapshot());
        $postingService->getApDocCmdRepository()->store($rootEntity, True, True);        
    }
    
    
    protected function preReserve(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {}

    protected function afterReserve(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {}


}