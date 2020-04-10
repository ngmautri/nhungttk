<?php
namespace Procure\Domain\Shared;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Constants
{

    /**
     *
     * @var string
     */
    const PERIOD_STATUS_ARCHIVED = 'A';

    const SYS_NUMBER_UNASSIGNED = '0000000';

    // ====================
    // ACCOUNTING
    const DOC_STATUS_DRAFT = 'draft';

    const DOC_STATUS_OPEN = 'open';

    const DOC_STATUS_CLOSED = 'closed';

    const DOC_STATUS_POSTED = 'posted';

    const DOC_STATUS_ARCHIVED = 'archived';

    const DOC_STATUS_REVERSED = 'reversed';

    // =====================
    const TRANSACTION_STATUS_OPEN = 'open';

    const TRANSACTION_STATUS_CLOSED = 'closed';

    const TRANSACTION_STATUS_COMPLETED = 'completed';

    const TRANSACTION_STATUS_UNCOMPLETED = 'uncompleted';

    // =====================
    const PAYMENT_STATUS_OPEN = 'open';

    const PAYMENT_STATUS_PAID = 'paid';

    const PAYMENT_STATUS_UNPAID = 'unpaid';

    // =====================
    const WH_TRANSACTION_STATUS_OPEN = 'draft';

    const WH_TRANSACTION_STATUS_POSTED = 'posted';

    const WH_TRANSACTION_IN = 'IN';

    const WH_TRANSACTION_OUT = 'OUT';

    // =====================
    const TRANSACTION_TYPE_PURCHASED = 'purchased';

    const TRANSACTION_TYPE_PURCHASED_RETURN = 'purchase-returned';

    const TRANSACTION_TYPE_SALE = 'sale';

    const TRANSACTION_TYPE_SALE_RETURN = 'sale-returned';

    // =====================
    const ITEM_WITH_SERIAL_NO = 'SN';

    const ITEM_WITH_BATCH_NO = 'B';

    const ITEM_TYPE_ITEM = 'ITEM';

    const ITEM_TYPE_SERVICE = 'SERVICE';

    const ITEM_TYPE_SOFTWARE = 'SOFTWARE';

    // =============================
    // ACCOUNT PAYABLE
    // =============================
    
    const PROCURE_DOC_TYPE_PR = 'AP-100';

    const PROCURE_DOC_TYPE_REVERSAL = 'AP-100-1';

    const PROCURE_DOC_TYPE_QUOTE = 'AP-110';

    const PROCURE_DOC_TYPE_QUOTE_REVERSAL = 'AP-110-1';

    const PROCURE_DOC_TYPE_PO = 'AP-120';

    const PROCURE_DOC_TYPE_PO_FROM_QOUTE = 'AP-120-110';

    const PROCURE_DOC_TYPE_PO_REVERSAL = 'AP-120-1';

    const PROCURE_DOC_TYPE_GR = 'AP-130';

    const PROCURE_DOC_TYPE_GR_FROM_PO = 'AP-130-120';

    const PROCURE_DOC_TYPE_GR_REVERSAL = 'AP-130-1';

    // Goods Receitp from PO.
    const PROCURE_DOC_TYPE_RETURN = 'AP-135';

    const PROCURE_DOC_TYPE_RETURN_REVERSAL = 'AP-135-1';

    const PROCURE_DOC_TYPE_INVOICE = 'AP-140';

    const PROCURE_DOC_TYPE_INVOICE_PO = 'AP-140-120';

    // AP Invoice from PO
    const PROCURE_DOC_TYPE_INVOICE_REVERSAL = 'AP-140-1';

    const PROCURE_DOC_TYPE_CREDIT_MEMO = 'AP-150';

    const PROCURE_DOC_TYPE_CREDIT_MEMO_REVERSAL = 'AP-150-1';

    const PROCURE_DOC_TYPE_AP_ACCRUAL = 'AP-160';

    const PROCURE_DOC_TYPE_AP_ACCRUAL_REVERSAL = 'AP-160-1';

    // =====================

    /**
     * Goods receipt, Invoice Not receipt.
     *
     * @var string
     */
    const PROCURE_TRANSACTION_TYPE_GRNI = 'GR-NI';

    /**
     * Goods and Invoice receipt.
     *
     * @var string
     */
    const PROCURE_TRANSACTION_TYPE_GRIR = 'GR-IR';

    /**
     * Goods Not receipt and Invoice receipt.
     *
     * @var string
     */
    const PROCURE_TRANSACTION_TYPE_IRNG = 'IR-NG';

    // =====================
    const PROCURE_TRANSACTION_STATUS_PENDING = 'pending';

    const PROCURE_TRANSACTION_STATUS_CLEARED = 'cleared';

    const PROCURE_TRANSACTION_STATUS_CLOSED = 'closed';

    const FORM_ACTION_ADD = 'ADD';

    const FORM_ACTION_EDIT = 'EDIT';

    const FORM_ACTION_SHOW = 'SHOW';

    const FORM_ACTION_DELETE = 'DELETE';

    const FORM_ACTION_REVIEW = 'REVIEW';

    const FORM_ACTION_REVIEW_AMENDMENT = 'REVIEW_AMENDMENT';

    const FORM_ACTION_AP_FROM_PO = 'AP-PO';

    const FORM_ACTION_AP_FROM_GR = 'AP-GR';

    const FORM_ACTION_PO_FROM_QO = 'QO-PO';

    const FORM_ACTION_GR_FROM_PO = 'GR-PO';

    const AJAX_OK = '1';

    const AJAX_FAILED = '-1';
}

