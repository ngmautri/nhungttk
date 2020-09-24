<?php
namespace Inventory\Domain\Contracts;

/**
 * Document Status
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TrxDocStatus

{

    const DRAFT = 'draft';

    const OPEN = 'open';

    const CLOSED = 'closed';

    const POSTED = 'posted';

    const AMENDING = 'amending';

    const ARCHIVED = 'archived';

    const REVERSED = 'reversed';
}