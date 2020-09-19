<?php
namespace Procure\Domain\Contracts;

/**
 * Document Status
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ProcureTrxStatus
{

    const OPEN = 'open';

    const CLOSED = 'closed';

    const COMPLETED = 'completed';

    const UNCOMPLETED = 'uncompleted';
}