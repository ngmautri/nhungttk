abt<?php
namespace HR\Domain\Salary\Contracts;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractSalarySchema implements SalaryInterface
{

    private $components;

    public function __construct()
    {
        $this->components = new ArrayCollection();
        $this->init();
    }

    abstract public function init();

    /**
     *
     * @param SalaryInterface $salaryComponent
     * @throws InvalidArgumentException
     */
    public function add(SalaryInterface $salaryComponent)
    {
        if (! $salaryComponent instanceof SalaryInterface) {
            throw new InvalidArgumentException("Individual Validator is required!");
        }

        $this->getComponents()->add($salaryComponent);
    }

    /**
     *
     * @param SalaryInterface $salaryComponent
     * @throws InvalidArgumentException
     */
    public function remove(SalaryInterface $salaryComponent)
    {
        if (! $salaryComponent instanceof SalaryInterface) {
            throw new InvalidArgumentException("Individual Validator is required!");
        }

        $this->getComponents()->removeElement($salaryComponent);
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getComponents()
    {
        return $this->components;
    }
}

