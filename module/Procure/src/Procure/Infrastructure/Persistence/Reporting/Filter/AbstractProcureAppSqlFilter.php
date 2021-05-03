<?php
namespace Procure\Infrastructure\Persistence\Reporting\Filter;

use Procure\Infrastructure\Persistence\Reporting\Contracts\ProcureAppSqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractProcureAppSqlFilter implements ProcureAppSqlFilterInterface
{

    protected $companyId;

    protected $sortBy;

    protected $sort;

    protected $limit;

    protected $offset;

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
