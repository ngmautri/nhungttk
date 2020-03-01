<?php
namespace Procure\Domain\APInvoice;

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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\APInvoice\GenericAPDoc::doReverse()
     */
    protected function doReverse(APSpecificationService $specService, APPostingService $postingService)
    {
        // create new reserval
        $rootEntity = APFactory::createAPDocument(APDocType::AP_INVOICE_REVERSAL);
        $rootEntity->makeSnapshot($this->makeSnapshot());
        $postingService->getApDocCmdRepository()->store($rootEntity, True, True);
    }

    protected function afterPost(APSpecificationService $specService, APPostingService $postingService)
    {}

    protected function prePost(APSpecificationService $specService, APPostingService $postingService)
    {}

    protected function preReserve(APSpecificationService $specService, APPostingService $postingService)
    {}

    protected function specificHeaderValidation(APSpecificationService $specService, $isPosting = false)
    {}

    protected function specificValidation(APSpecificationService $specService, $isPosting = false)
    {}

    protected function afterReserve(APSpecificationService $specService, APPostingService $postingService)
    {}

    protected function raiseEvent()
    {}

    protected function doPost(APSpecificationService $specService, APPostingService $postingService)
    {}

    protected function specificRowValidation(ApDocRow $row, APSpecificationService $specService, $isPosting = false)
    {}
}