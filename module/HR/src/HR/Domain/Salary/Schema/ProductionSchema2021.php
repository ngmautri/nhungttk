<?php
namespace HR\Domain\Salary\Schema;

use HR\Domain\Salary\Contracts\AbstractSalarySchema;
use HR\Domain\Salary\Contracts\BasicSalary2021;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ProductionSchema2021 extends AbstractSalarySchema
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Salary\Contracts\AbstractSalarySchema::init()
     */
    public function init()
    {
        $salaryComponent = new BasicSalary2021();
        $this->add($salaryComponent);
    }
}

