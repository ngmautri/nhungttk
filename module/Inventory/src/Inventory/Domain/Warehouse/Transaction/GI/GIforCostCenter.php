<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Application\Notification;
use Inventory\Domain\Service\TransactionPostingService;
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
     * \
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
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificValidation()
     */
    protected function specificValidation($notification = null)
    {
        // no need
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificHeaderValidation()
     */
    protected function specificHeaderValidation($notification = null)
    {}

    /**
     * It require Cost Center.
     *
     * @param TransactionRow $row
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificRowValidation()
     */
    protected function specificRowValidation($row, $notification = null, $isPosting = false)
    {
        if ($notification == null) {
            $notification = new Notification();
        }

        if ($row == null)
            $notification->addError("Row is empty");

        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($row->getCostCenter())) {
            $notification->addError("Cost Center is required!");
        } else {

            /**
             *
             * @var AbstractSpecificationForCompany $spec ;
             */
            $spec = $this->sharedSpecificationFactory->getCostCenterExitsSpecification();
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
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::afterPost()
     */
    protected function afterPost(TransactionPostingService $postingService = null, $notification = null)
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::prePost()
     */
    protected function prePost(TransactionPostingService $postingService = null, $notification = null)
    {}


   
}