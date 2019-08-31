<?php
namespace Procure\Domain\APInvoice\Factory;

use Procure\Domain\APInvoice\APDocType;
use Procure\Domain\APInvoice\APInvoice;
use Procure\Domain\APInvoice\APInvoiceReserval;
use Procure\Domain\APInvoice\APCreditMemo;
use Procure\Domain\APInvoice\APCreditMemoReserval;
use Procure\Domain\PurchaseOrder\AbstractPO;
use Procure\Domain\APInvoice\APDocSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APFactory
{

    public static function createAPDocument($apDocTypeId)
    {
        switch ($apDocTypeId) {

            case APDocType::AP_INVOICE:
                $entityRoot = new APInvoice();
                break;

            case APDocType::AP_INVOICE_REVERSAL:
                $entityRoot = new APInvoiceReserval();
                break;

            case APDocType::AP_CREDIT_MEMO:
                $entityRoot = new APCreditMemo();
                break;

            case APDocType::AP_CREDIT_MEMO_REVERSAL:
                $entityRoot = new APCreditMemoReserval();
                break;
        }
        return $entityRoot;
    }
    
   /**
    * 
    * @param AbstractPO $po
    * @return \Procure\Domain\APInvoice\APInvoice
    */
    public static function createAPInvoiceFromPO(AbstractPO $po)
    {
        $rootEntity = self::createAPDocument(APDocType::AP_INVOICE);
        $snapshot = $po->makeSnapshot();
        $apSnapshot = new APDocSnapshot();
        
        //$apSnapshot- = $snapshot->id;
        //$apSnapshot- = $snapshot->token;
        $apSnapshot->vendorName = $snapshot->vendorName;
        $apSnapshot->invoiceNo = $snapshot->invoiceNo;
        $apSnapshot->invoiceDate = $snapshot->invoiceDate;
        $apSnapshot->currencyIso3 = $snapshot->currencyIso3;
        $apSnapshot->exchangeRate = $snapshot->exchangeRate;
        $apSnapshot->remarks = $snapshot->remarks;
        $apSnapshot->createdOn = $snapshot->createdOn;
        $apSnapshot->currentState = $snapshot->currentState;
        $apSnapshot->isActive = $snapshot->isActive;
        $apSnapshot->lastchangeOn = $snapshot->lastchangeOn;
        $apSnapshot->postingDate = $snapshot->postingDate;
        $apSnapshot->grDate = $snapshot->grDate;
        $apSnapshot->sapDoc = $snapshot->sapDoc;
        $apSnapshot- = $snapshot->contractNo;
        $apSnapshot- = $snapshot->contractDate;
        $apSnapshot- = $snapshot->quotationNo;
        $apSnapshot- = $snapshot->quotationDate;
        $apSnapshot- = $snapshot->sysNumber;
        $apSnapshot- = $snapshot->revisionNo;
        $apSnapshot- = $snapshot->deliveryMode;
        $apSnapshot- = $snapshot->incoterm;
        $apSnapshot- = $snapshot->incotermPlace;
        $apSnapshot- = $snapshot->paymentTerm;
        $apSnapshot- = $snapshot->docStatus;
        $apSnapshot- = $snapshot->workflowStatus;
        $apSnapshot- = $snapshot->transactionStatus;
        $apSnapshot- = $snapshot->docType;
        $apSnapshot- = $snapshot->paymentStatus;
        $apSnapshot- = $snapshot->totalDocValue;
        $apSnapshot- = $snapshot->totalDocTax;
        $apSnapshot- = $snapshot->totalDocDiscount;
        $apSnapshot- = $snapshot->totalLocalValue;
        $apSnapshot- = $snapshot->totalLocalTax;
        $apSnapshot- = $snapshot->totalLocalDiscount;
        $apSnapshot- = $snapshot->reversalBlocked;
        $apSnapshot- = $snapshot->uuid;
        $apSnapshot- = $snapshot->vendor;
        $apSnapshot- = $snapshot->pmtTerm;
        $apSnapshot- = $snapshot->warehouse;
        $apSnapshot- = $snapshot->createdBy;
        $apSnapshot- = $snapshot->lastchangeBy;
        $apSnapshot- = $snapshot->currency;
        $apSnapshot- = $snapshot->paymentMethod;
        $apSnapshot- = $snapshot->localCurrency;
        $apSnapshot- = $snapshot->docCurrency;
        $apSnapshot- = $snapshot->incoterm2;
        
        
        
        
        return $entityRoot;
    }
}