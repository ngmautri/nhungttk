<?php
namespace Application\Domain\Company\Contracts\Account;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SystemAcountType
{

    const BANK = 'bk';

    const CURRENT_ASSET = 'fa';

    const FIXED_ASSET = 'cl';

    const INVENTORY = 'nl';

    const NONE_CURRENT_ASSET = 'eq';

    const PREPAYMENT = 'dc';
}
