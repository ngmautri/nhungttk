<?php
namespace Application\Model;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Constants

{

    // =============USER ===========
    const USER_ROLE_DEFAULT = 'member';

    const USER_ROLE_SUPER_ADMINISTRATOR = 'super-administrator';

    const USER_ROLE_ADMINISTRATOR = 'administrator';

    // =============USER ===========
    const OS_LINUX = "Linux";

    const OS_WINNT = "WINNT";

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    /**
     * Inventory
     *
     * @var string _06a19815c7af6d8df4cfe6ab657ec477fe284cda72fbf12cce02dabdd4ce51a8
     */
    const INVENTORY_HASH_ID = "7a1eabc3deb7fd02ceb1e16eafc41073";

    /**
     * Procure
     *
     * @var string
     */
    const PROCURE_HASH_ID = "906755664503f7af496028c471cfffcc";

    /**
     * Finance
     *
     * @var string
     */
    const FINANCE_HASH_ID = "906755664503f7af496028c471cfffcc";

    /**
     * HR
     *
     * @var string
     */
    const HR_HASH_ID = "adab7b701f23bb82014c8506d3dc784e";

    /**
     * BP
     *
     * @var string
     */
    const BP_HASH_ID = "5cfdb867e96374c7883b31d6928cc4cb";

    /**
     * Setting
     *
     * @var string
     */
    const SETTING_HASH_ID = "7dc22b2c6a992f0232345df41303f5ea";

    /**
     * Period is open
     *
     * @var string
     */
    const PERIOD_STATUS_OPEN = 'N';

    /**
     * Period is closted
     *
     * @var string
     */
    const PERIOD_STATUS_CLOSED = 'C';

    /**
     * Closing
     *
     * @var string
     */
    const PERIOD_STATUS_CLOSING = 'Y';

    /**
     * Archived
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

    // =====================
    // ACCOUNT PAYABLE
    const PROCURE_DOC_TYPE_PR = 'AP-100';

    const PROCURE_DOC_TYPE_REVERSAL = 'AP-100-1';

    const PROCURE_DOC_TYPE_QUOTE = 'AP-110';

    const PROCURE_DOC_TYPE_QUOTE_REVERSAL = 'AP-110-1';

    const PROCURE_DOC_TYPE_PO = 'AP-120';

    const PROCURE_DOC_TYPE_PO_REVERSAL = 'AP-120-1';

    const PROCURE_DOC_TYPE_GR = 'AP-130';

    const PROCURE_DOC_TYPE_GR_REVERSAL = 'AP-130-1';

    const PROCURE_DOC_TYPE_RETURN = 'AP-135';

    const PROCURE_DOC_TYPE_RETURN_REVERSAL = 'AP-135-1';

    const PROCURE_DOC_TYPE_INVOICE = 'AP-140';

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

    const FORM_ACTION_AP_FROM_PO = 'AP-PO';

    const FORM_ACTION_AP_FROM_GR = 'AP-GR';

    const FORM_ACTION_PO_FROM_QO = 'QO-PO';

    const AJAX_OK = '1';

    const AJAX_FAILED = '-1';

    public static function v4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), 

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff), 

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000, 

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000, 

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }
}

