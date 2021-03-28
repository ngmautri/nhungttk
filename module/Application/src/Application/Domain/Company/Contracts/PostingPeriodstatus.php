<?php
namespace Application\Domain\Company\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostingPeriodstatus
{

    /*
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
}
