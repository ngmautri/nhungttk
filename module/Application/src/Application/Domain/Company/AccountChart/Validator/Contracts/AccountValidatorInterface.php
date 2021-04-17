<?php
namespace Application\Domain\Company\AccountChart\Validator\Contracts;

use Application\Domain\Company\AccountChart\AbstractAccount;
use Application\Domain\Company\AccountChart\AbstractChart;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface AccountValidatorInterface
{

    /**
     *
     * @param AbstractChart $rootEntity
     * @param AbstractAccount $localEntity
     */
    public function validate(AbstractChart $rootEntity, AbstractAccount $localEntity);
}

