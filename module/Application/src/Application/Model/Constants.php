<?php
namespace Application\Model;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Constants
{
    const OS_LINUX = "Linux";
    const OS_WINNT = "WINNT";
    
    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
    
    
    /**
     * Inventory 
     * @var string
     * _06a19815c7af6d8df4cfe6ab657ec477fe284cda72fbf12cce02dabdd4ce51a8
     */
    const INVENTORY_HASH_ID = "7a1eabc3deb7fd02ceb1e16eafc41073";
    
    /**
     * Procure
     * @var string
     */
    const PROCURE_HASH_ID = "906755664503f7af496028c471cfffcc";
    
    /**
     * Finance
     * @var string
     */
    const FINANCE_HASH_ID = "906755664503f7af496028c471cfffcc";
    

    /**
     * HR
     * @var string
     */
    const HR_HASH_ID = "adab7b701f23bb82014c8506d3dc784e";
   
    
    /**
     * BP
     * @var string
     */
    const BP_HASH_ID = "5cfdb867e96374c7883b31d6928cc4cb";
    
    /**
     * Setting
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
    
    // =====================
    
    /**
     * Goods receipt, Invoice Not receipt.
     * @var string
     */
    const PROCURE_TRANSACTION_TYPE_GRNI = 'GR-NI';
    
    /**
     *  Goods and Invoice receipt.
     * @var string
     */
    const PROCURE_TRANSACTION_TYPE_GRIR = 'GR-IR';
    
     /**
     *  Goods Not receipt and Invoice receipt.
     * @var string
     */
    const PROCURE_TRANSACTION_TYPE_IRNG = 'IR-NG';
    
    // =====================
    
    const PROCURE_TRANSACTION_STATUS_PENDING = 'pending';
    const PROCURE_TRANSACTION_STATUS_CLEARED = 'cleared';
    const PROCURE_TRANSACTION_STATUS_CLOSED = 'closed';
    
    const FORM_ACTION_ADD ='ADD';
    const FORM_ACTION_EDIT ='EDIT';
    const FORM_ACTION_SHOW ='SHOW';
    const FORM_ACTION_DELETE ='DELETE';
    
    
    
    
}

