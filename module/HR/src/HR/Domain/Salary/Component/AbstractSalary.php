<?php
namespace HR\Domain\Salary\Component;

use Application\Domain\Shared\ValueObject;
use HR\Domain\Salary\Contracts\SalaryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractSalary extends ValueObject implements SalaryInterface
{

    public function makeSnapshot()
    {}

    public function getAttributesToCompare()
    {}
}