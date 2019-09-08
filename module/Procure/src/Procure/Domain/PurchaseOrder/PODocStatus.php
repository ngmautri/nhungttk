<?php
namespace Procure\Domain\PurchaseOrder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PODocStatus

{

    // ACCOUNTING
    const DOC_STATUS_DRAFT = 'draft';
    
    const DOC_STATUS_OPEN = 'open';
    
    const DOC_STATUS_CLOSED = 'closed';
    
    const DOC_STATUS_POSTED = 'posted';
    
    const DOC_STATUS_ARCHIVED = 'archived';
    
    const DOC_STATUS_REVERSED = 'reversed';
}