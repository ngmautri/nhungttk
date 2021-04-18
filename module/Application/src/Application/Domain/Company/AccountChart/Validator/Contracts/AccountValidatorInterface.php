<?php
namespace Application\Domain\Company\AccountChart\Validator\Contracts;

use Application\Domain\Company\AccountChart\AbstractAccount;
use Application\Domain\Company\AccountChart\AbstractChart;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;

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
    public function validate(BaseChart $rootEntity, BaseAccount $localEntity);
}

