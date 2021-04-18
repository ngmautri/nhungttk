<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine\Filter;

use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyQuerySqlFilter implements CompanySqlFilterInterface
{

    public $isActive;

    public $companyId;

    public $sortBy;

    public $sort;

    public $limit;

    public $offset;

    public function __toString()
    {
        $f = "CompanyQuerySqlFilter_ID%s_sortBy_%s_sort_%s_limit_%s_offset_%s";
        return \sprintf($f, $this->getCompanyId(), $this->getSortBy(), $this->getSort(), $this->getLimit(), $this->getOffset());
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     *
     * @param mixed $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     *
     * @return mixed
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     *
     * @param mixed $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }

    /**
     *
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     *
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     *
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     *
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     *
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     *
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
}
