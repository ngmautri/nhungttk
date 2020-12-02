<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ProcureGoodsFlow;
use Procure\Domain\Contracts\ProcureTrxStatus;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\GoodsReceipt\Validator\ValidatorFactory;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\ProcureDocStatus;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRFromPO extends GenericGoodsReceipt
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::specify()
     */
    public function specify()
    {
        $this->flow = ProcureGoodsFlow::IN;
        $this->docType = ProcureDocType::GR_FROM_PO;
    }

    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRFromPO
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     *
     * @param PODoc $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function createFromPo(PODoc $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        if (! $sourceObj instanceof PODoc) {
            throw new \InvalidArgumentException("PO Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new \InvalidArgumentException("PO Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::POSTED) {
            throw new \RuntimeException("PO document is not posted!");
        }

        if ($sourceObj->getTransactionStatus() == ProcureTrxStatus::COMPLETED) {
            throw new \RuntimeException("PO is completed!");
        }

        if ($options == null) {
            throw new \InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new GRFromPO();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(ProcureDocType::GR_FROM_PO); // important.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $validationService = ValidatorFactory::create($instance->getDocType(), $sharedService);

        $instance->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            /**
             *
             * @var \Procure\Domain\PurchaseOrder\PORow $r ;
             */

            // ignore completed row;
            if ($r->getOpenGrBalance() == 0) {
                continue;
            }

            $grRow = GrRow::createFromPoRow($r, $options);
            // echo sprintf("\n %s, PoRowId %s, %s" , $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
            $instance->addRow($grRow);

            $instance->validateRow($grRow, $validationService->getRowValidators());
        }
        return $instance;
    }

    public function saveFromPO(GRSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if (! $this->getDocStatus() == ProcureDocStatus::DRAFT) {
            throw new InvalidArgumentException(sprintf("PO is already posted/closed or being amended! %s", $this->getId()));
        }

        if ($this->getDocRows() == null) {
            throw new InvalidArgumentException(sprintf("Documment is empty! %s", $this->getId()));
        }

        if (! $this->getDocType() == ProcureDocType::GR_FROM_PO) {
            throw new InvalidArgumentException(sprintf("Doctype is not vadid! %s", $this->getDocType()));
        }

        if ($options == null) {
            throw new InvalidArgumentException("Comnand Options not found!");
        }

        // Update Good Receipt Date and WH
        if ($snapshot !== null) {
            $this->setGrDate($snapshot->getGrDate());
            $this->setWarehouse($snapshot->getWarehouse());
        }

        $createdDate = new \Datetime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $validationService = ValidatorFactory::create($instance->getDocType(), $sharedService);
        $this->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());
        if ($this->hasErrors()) {
            throw new OperationFailedException($this->getErrorMessage());
        }

        $this->clearEvents();
        $rootSnapshot = $postingService->getCmdRepository()->store($this);

        return $rootSnapshot;
    }
}