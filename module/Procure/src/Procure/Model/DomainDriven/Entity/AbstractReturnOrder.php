<?php
namespace Procure\Model\DomainDriven\Repository\Doctrine;

/**
 * Abtract Factory 
 * @author Nguyen Mau Tri
 *
 */
Abstract class AbstractPurchaseRequest
{
    abstract public function createPurchaseRequest();
    abstract public function createQuotationOrder();
    abstract public function createPurchaseOrder();
    abstract public function createAPInvoice();
    abstract public function createCreditMemo();
    abstract public function createReturnOrder();
}
