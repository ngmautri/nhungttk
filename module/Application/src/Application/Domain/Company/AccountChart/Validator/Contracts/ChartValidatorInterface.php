<?php
namespace Application\Domain\Company\AccountChart\Validator\Contracts;

use Application\Domain\Company\AccountChart\AbstractChart;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ChartValidatorInterface
{

    public function validate(AbstractChart $rootEntity);
}

