<?php

namespace Application\Model;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Constants {
    
   /**
    * Period is open
    * @var string
    */
    Const PERIOD_STATUS_OPEN = 'N';
    
    /**
     * Period is closted
     * @var string
     */
    Const PERIOD_STATUS_CLOSED = 'C';
    
    /**
     * Closing
     * @var string
     */
    Const PERIOD_STATUS_CLOSING = 'Y';
    
    /**
     * Archived
     * @var string
     */
    Const PERIOD_STATUS_ARCHIVED = 'A';    
    
    //====================
        
    Const DOC_STATUS_DRAFT = 'draft';
    Const DOC_STATUS_OPEN = 'open';
    Const DOC_STATUS_CLOSED = 'closed';
    Const DOC_STATUS_POSTED = 'posted';
    Const DOC_STATUS_ARCHIVED = 'archived';
    
    
    //=====================
    Const TRANSACTION_STATUS_OPEN = 'open';
    Const TRANSACTION_STATUS_CLOSED = 'closed';
    Const TRANSACTION_STATUS_COMPLETED='completed';
    Const TRANSACTION_STATUS_UNCOMPLETED='uncompleted';
    
    //=====================
    Const PAYMENT_STATUS_OPEN = 'open';
    Const PAYMENT_STATUS_PAID='paid';
    Const PAYMENT_STATUS_UNPAID='unpaid';    
    
    //=====================
    Const WH_TRANSACTION_STATUS_OPEN = 'draft';
    Const WH_TRANSACTION_STATUS_POSTED='posted';

    Const WH_TRANSACTION_IN = 'IN';
    Const WH_TRANSACTION_OUT='OUT';
    
    
    //=====================
    Const TRANSACTION_TYPE_PURCHASED = 'purchased';
    Const TRANSACTION_TYPE_PURCHASED_RETURN = 'purchase-returned';
    Const TRANSACTION_TYPE_SALE='sale';
    Const TRANSACTION_TYPE_SALE_RETURN='sale-returned';
    
    //=====================
    
    Const ITEM_WITH_SERIAL_NO ='SN';
    Const ITEM_WITH_BATCH_NO ='B';
    
    Const ITEM_TYPE_ITEM ='ITEM';
    Const ITEM_TYPE_SERVICE ='SERVICE';
    Const ITEM_TYPE_SOFTWARE ='SOFTWARE';
    
}

