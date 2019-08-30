<?php
namespace Procure\Domain\APInvoice\Factory;

use Procure\Domain\APInvoice\APDocType;
use Procure\Domain\APInvoice\APInvoice;
use Procure\Domain\APInvoice\APInvoiceReserval;
use Procure\Domain\APInvoice\APCreditMemo;
use Procure\Domain\APInvoice\APCreditMemoReserval;

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
}