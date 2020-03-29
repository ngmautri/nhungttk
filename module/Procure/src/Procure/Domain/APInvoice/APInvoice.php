<?php
namespace Procure\Domain\APInvoice;

use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\APSpecificationService;
use Procure\Domain\APInvoice\Factory\APFactory;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Exception\Ap\ApInvalidArgumentException;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Exception\Ap\ApInvalidOperationException;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoice extends GenericAPDoc
{

    /**
     *
     * @param PODoc $sourceObj
     * @throws ApInvalidArgumentException
     * @throws ApInvalidOperationException
     * @return \Procure\Domain\APInvoice\APInvoice
     */
    public static function createFromPo(PODoc $sourceObj)
    {
        if (! $sourceObj instanceof PODoc) {
            throw new ApInvalidArgumentException("PO Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new ApInvalidArgumentException("PO Entity  is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new ApInvalidOperationException("PO document is not posted!");
        }

        if ($sourceObj->getTransactionStatus() == \Procure\Domain\Shared\Constants::TRANSACTION_STATUS_COMPLETED) {
            throw new ApInvalidOperationException("PO is completed!");
        }

        /**
         *
         * @var \Procure\Domain\APInvoice\APInvoice $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);
        $instance->setIsDraft(1);
        $instance->setIsPosted(0);
        $instance->setDocStatus(ProcureDocStatus::DOC_STATUS_DRAFT);
        $instance->setUuid(Uuid::uuid4()->toString());
        $instance->setToken($instance->getUuid());
        foreach ($rows as $r) {
            $grRow = APDocRow::createFromPoRow($r);
            // echo sprintf("\n %s, PoRowId %s, %s" , $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
            $instance->addRow($grRow);
        }
        return $instance;
    }

    public static function createSnapshotProps()
    {
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "public $" . $propertyName . ";";
        }
    }

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