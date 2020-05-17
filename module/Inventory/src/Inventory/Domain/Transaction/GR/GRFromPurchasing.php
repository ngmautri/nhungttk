<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Contracts\PostingServiceInterface;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Transaction\GoodsReceipt;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\Shared\ProcureDocStatus;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromPurchasing extends GoodsReceipt implements GoodsReceiptInterface
{

    public function __construct()
    {
        $this->movementType = TransactionType::GR_FROM_PURCHASING;
        $this->movementFlow = TransactionFlow::WH_TRANSACTION_IN;
    }

    /**
     *
     * @param GRDoc $sourceObj
     * @param TrxSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param PostingServiceInterface $postingService
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GR\GRFromPurchasing
     */
    public function createFromProcureGR(GRDoc $sourceObj, TrxSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {
        if (! $sourceObj instanceof GRDoc) {
            throw new InvalidArgumentException("GR Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("Source Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException("Source document is not posted!");
        }

        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        $sourceObj->convertTo($this);

        // overwrite.
        // $this->setDocType(\Procure\Domain\Shared\Constants::PROCURE_DOC_TYPE_INVOICE_PO); // important.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $this->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $this->validateHeader($headerValidators);

        foreach ($rows as $r) {

            /**
             *
             * @var TrxRow $r ;
             */

            // ignore none-inventory item;
            if (! $r->getIsInventoryItem()) {
                continue;
            }

            $localEntity = GRFromPurchasingRow::createFromPurchaseGrRow($this, $r, $options);
            $this->addRow($localEntity);
            $this->validateRow($localEntity, $rowValidators);
        }
        return $this;
    }
}