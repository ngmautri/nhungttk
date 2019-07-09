<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Inventory\Domain\Warehouse\Transaction\GoodsIssue;
use Inventory\Domain\Warehouse\Transaction\GoodsIssueInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Application\Notification;

/**
 * Machine ID is required, exchange part.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforRepairMachine extends GoodsIssue implements GoodsIssueInterface
{

    public function specify()
    {
        $this->movementType = TransactionType::GI_FOR_REPAIR_MACHINE_WITH_EX;
        $this->movementFlow = TransactionFlow::WH_TRANSACTION_OUT;
    }

    public function afterPost()
    {}

    public function prePost()
    {}

    public function specificValidation($notification = null)
    {}

    public function specificHeaderValidation($notification = null)
    {}

    /**
     *
     * @param TransactionRow $row
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificRowValidation()
     */
    public function specificRowValidation($row, $notification = null, $isPosting = false)
    {
        if ($notification == null) {
            $notification = new Notification();
        }

        if ($row == null)
            $notification->addError("Row is empty");

        if ($this->sharedSpecificationFactory == null)
            $notification->addError("Validator is not found");

        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($row->getIssueFor())) {
            $notification->addError("Machine ID is required!");
        } else {

            /**
             *
             * @var AbstractSpecificationForCompany $spec ;
             */
            $spec = $this->sharedSpecificationFactory->getIsFixedAssetSpecification();
            $subject = array(
                "companyId" => $this->company,
                "itemId" => $row->getIssueFor()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $notification->addError("Item issued for not exits #" . $row->getIssueFor());
            }

            if ($row->getIssueFor() == $row->getItem()) {
                $notification->addError("Same item");
            }
        }

        return $notification;
    }
}