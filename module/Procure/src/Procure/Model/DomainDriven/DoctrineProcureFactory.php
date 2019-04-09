<?php
namespace Procure\Model\DomainDriven\Repository\Doctrine;

use Procure\Model\DomainDriven\Entity\Doctrine\DoctrinePurchaseRequest;

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

    /**
     * 
     * {@inheritDoc}
     * @see \Procure\Model\DomainDriven\Repository\Doctrine\AbstractProcureFactory::createPurchaseRequest()
     */
    public function createPurchaseRequest()
    {
        return new DoctrinePurchaseRequest();
    }

    public function createQuotationOrderRepository()
    {}

    public function createCreditMemoRepository()
    {}
}
