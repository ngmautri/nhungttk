<?php
namespace Application\Domain\Company\AccountChart\Validator\Contracts;

use Application\Domain\Company\AccountChart\BaseChart;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ChartValidatorInterface
{

    public function validate(BaseChart $rootEntity);
}

