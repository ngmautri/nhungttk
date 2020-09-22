<?php
namespace Procure\Domain\Contracts;

/**
 * Document Type
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ProcureDocType
{

    // ==================================
    // Purchase Request
    // ==================================
    const PR = 'AP-100';

    const PR_REVERSAL = 'AP-100-1';

    // ==================================
    // Quotation
    // ==================================
    const QUOTE = 'AP-110';

    const QUOTE_REVERSAL = 'AP-110-1';

    // ==================================
    // Purchase Order
    // ==================================
    const PO = 'AP-120';

    const PO_FROM_QOUTE = 'AP-120-110';

    const PO_REVERSAL = 'AP-120-1';

    // ==================================
    // Goods Receipt
    // ==================================
    const GR = 'AP-130';

    const GR_FROM_PO = 'AP-130-120';

    const GR_FROM_INVOICE = 'AP-130-140';

    const GR_REVERSAL = 'AP-130-1';

    const GR_REVERSAL_FROM_AP_RESERVAL = 'AP-130-140-1';

    // ==================================
    // Goods Return
    // ==================================
    const GOODS_RETURN = 'AP-135';

    const GOODS_RETURN_REVERSAL = 'AP-135-1';

    const GOODS_RETURN_FROM_WH_RETURN = 'AP-135-2';

    // ==================================
    // AP Invoice
    // ==================================
    const INVOICE = 'AP-140';

    const INVOICE_FROM_PO = 'AP-140-120';

    const INVOICE_REVERSAL = 'AP-140-1';

    const INVOICE_CORRECTION = 'AP-140-2';

    // ==================================
    // AP Credit Memo
    // ==================================
    const CREDIT_MEMO = 'AP-150';

    const CREDIT_MEMO_REVERSAL = 'AP-150-1';

    const CREDIT_MEMO_FOR_RETURN = 'AP-150-2';

    // ==================================
    // AP Accrual
    // ==================================
    const AP_ACCRUAL = 'AP-160';

    const AP_ACCRUAL_REVERSAL = 'AP-160-1';
}