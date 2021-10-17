<?php
namespace Procure\Domain\Contracts;

/**
 * Transation Status
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ProcureTrxStatus
{

    const OPEN = 'open';

    const CLOSED = 'closed';

    const HAS_QUOTATION = 'quoted';

    const COMMITTED = 'committed';

    const PARTIAL_COMMITTED = 'parial committed';

    const NEED_ACTION = 'acction needed';

    const COMPLETED = 'completed';

    const PARTIAL_COMPLETED = 'partial completed';

    const PENDING = 'pending';

    const UNCOMPLETED = 'uncompleted';

    const CLEARED = 'cleared';
}