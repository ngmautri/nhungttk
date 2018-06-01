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
   
}

