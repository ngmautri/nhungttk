<?php
namespace HR\Application\Service\Search\ZendSearch\Individual\Filter;

use Application\Application\Service\Search\Contracts\QueryFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IndividualQueryFilter implements QueryFilterInterface
{

    protected $isApplicant;

    protected $isEmployee;

    public function __toString()
    {}

    /**
     *
     * @return mixed
     */
    public function getIsApplicant()
    {
        return $this->isApplicant;
    }

    /**
     *
     * @return mixed
     */
    public function getIsEmployee()
    {
        return $this->isEmployee;
    }

    /**
     *
     * @param mixed $isApplicant
     */
    public function setIsApplicant($isApplicant)
    {
        $this->isApplicant = $isApplicant;
    }

    /**
     *
     * @param mixed $isEmployee
     */
    public function setIsEmployee($isEmployee)
    {
        $this->isEmployee = $isEmployee;
    }
}
