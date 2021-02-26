<?php
namespace Application\Infrastructure\Persistence\Filter;

use Application\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DefaultListSqlFilter implements SqlFilterInterface
{

    protected $sortBy;

    protected $sort;

    protected $limit;

    protected $offset;

    protected $companyId;

    /**
     *
     * @return string
     */
    public function __toString()
    {
        $format = '_%s_%s_%s_%s_%s';
        return \sprintf($format, $this->getSort(), $this->getSortBy(), $this->getLimit(), $this->getOffset(), $this->getCompanyId());
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
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
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
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
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
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
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
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
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
}
