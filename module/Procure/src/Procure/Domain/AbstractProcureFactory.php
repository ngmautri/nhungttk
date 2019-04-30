<?php
namespace Procure\Model\Domain;

/**
 * Abtract Factory
 * @author Nguyen Mau Tri (ngmautri@gmail.com)
 *
 */
abstract class AbstractProcureFactory
{

    abstract public function createPurchaseRequest();

    abstract public function createPurchaseRequestRepository();

    abstract public function createQuotationOrder();

    abstract public function createQuotationOrderRepository();

    abstract public function createPurchaseOrder();

    abstract public function createPurchaseOrderRepository();

    abstract public function createAPInvoice();

    abstract public function createAPInvoiceRepository();

    abstract public function createCreditMemoRepository();

    abstract public function createReturnOrder();

    abstract public function createReturnOrderRepository();
}
