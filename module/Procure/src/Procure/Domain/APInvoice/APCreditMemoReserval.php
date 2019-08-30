<?php
namespace Procure\Domain\APInvoice;

use Application\Notification;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\APSpecificationService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APCreditMemoReserval extends GenericAPDoc
{

    public function specify()
    {
        $this->docType = APDocType::AP_CREDIT_MEMO_REVERSAL;
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
    {}

    protected function doReverse(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {}

    protected function preReserve(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {}

    protected function afterReserve(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {}
}