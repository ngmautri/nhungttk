<?php
namespace Application\Model;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Constants
{

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

    
    
    // ====================
    const SYS_NUMBER_UNASSIGNED = '0000000';
    
    
    const DOC_STATUS_DRAFT = 'draft';

    const DOC_STATUS_OPEN = 'open';

    const DOC_STATUS_CLOSED = 'closed';

    const DOC_STATUS_POSTED = 'posted';

    const DOC_STATUS_ARCHIVED = 'archived';

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
    
    const PROCURE_DOC_TYPE_PR = 100;

    const PROCURE_DOC_TYPE_QUOTE = 110;

    const PROCURE_DOC_TYPE_PO = 120;

    const PROCURE_DOC_TYPE_GR = 130;

    const PROCURE_DOC_TYPE_RETURN = 135;

    const PROCURE_DOC_TYPE_AP = 140;

    const PROCURE_DOC_TYPE_CREDIT_MEMO = 150;

    const PROCURE_DOC_TYPE_AP_ACCRUAL = 160;
}

