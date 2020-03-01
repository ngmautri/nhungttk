<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Application\Notification;
use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Domain\Service\TransactionSpecificationService;
use Inventory\Domain\Service\TransactionValuationService;
use Inventory\Domain\Warehouse\Transaction\GoodsIssue;
use Inventory\Domain\Warehouse\Transaction\GoodsIssueInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforCostCenter extends GoodsIssue implements GoodsIssueInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specify()
     */
    public function specify()
    {
        $this->movementType = TransactionType::GI_FOR_COST_CENTER;
        $this->movementFlow = TransactionFlow::WH_TRANSACTION_OUT;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificRowValidation()
     */
    protected function specificRowValidation(TransactionSpecificationService $specificationService, TransactionRow $row, Notification $notification = null, $isPosting = false)
    {
        if ($notification == null) {
            $notification = new Notification();
        }

        if ($row == null)
            $notification->addError("Row is empty");

        if ($specificationService == null) {
            $notification->addError("Speficification not set");
        }

        if ($notification->hasErrors())
            return $notification;

        if ($specificationService->getSharedSpecificationFactory()->getNullorBlankSpecification()
            ->isSatisfiedBy($row->getCostCenter())) {
            $notification->addError("Cost Center is required!");
        } else {

            /**
             *
             * @var AbstractSpecificationForCompany $spec ;
             */
            $spec = $specificationService->getSharedSpecificationFactory()->getCostCenterExitsSpecification();
            $spec->setCompanyId($this->company);

            $subject = array(
                "companyId" => $this->company,
                "costCenter" => $row->getCostCenter()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $notification->addError("Cost Center not exits #" . $row->getCostCenter());
            }
        }

        return $notification;
    }

    protected function afterPost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null)
    {}

    protected function prePost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null)
    {}

    protected function specificValidation(TransactionSpecificationService $specificationService, Notification $notification = null)
    {}

    protected function specificHeaderValidation(TransactionSpecificationService $specificationService, Notification $notification = null)
    {}
    public function addTransactionRow(TransactionRow $transactionRow)
    {}

}