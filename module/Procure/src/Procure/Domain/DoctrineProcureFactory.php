<?php
namespace Procure\Model\Domain;

use Procure\Model\Domain\PurchaseRequest\Doctrine\DoctrinePurchaseRequest;
/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class DoctrineProcureFactory extends AbstractProcureFactory
{

    public function createAPInvoiceRepository()
    {}

    public function createPurchaseOrderRepository()
    {}

    public function createPurchaseOrder()
    {}

    public function createAPInvoice()
    {}

    public function createPurchaseRequestRepository()
    {}

    public function createQuotationOrder()
    {}

    public function createReturnOrder()
    {}

    public function createReturnOrderRepository()
    {}

    
    public function createPurchaseRequest()
    {
        $pr = new DoctrinePurchaseRequest();
        return $pr;
    }

    public function createQuotationOrderRepository()
    {}

    public function createCreditMemoRepository()
    {}
}
