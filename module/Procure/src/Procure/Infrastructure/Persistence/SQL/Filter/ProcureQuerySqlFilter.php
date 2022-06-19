<?php
namespace Procure\Infrastructure\Persistence\SQL\Filter;

use Procure\Infrastructure\Persistence\SQL\Contract\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ProcureQuerySqlFilter implements SqlFilterInterface
{

    public $companyId;

    public $sortBy;

    public $sort;

    public $limit;

    public $offset;

    public $resultPerPage;

    public $renderType;

    /**
     *
     * @return mixed
     */
    public function getResultPerPage()
    {
        return $this->resultPerPage;
    }

    /**
     *
     * @return mixed
     */
    public function getRenderType()
    {
        return $this->renderType;
    }

    /**
     *
     * @param mixed $resultPerPage
     */
    public function setResultPerPage($resultPerPage)
    {
        $this->resultPerPage = $resultPerPage;
    }

    /**
     *
     * @param mixed $renderType
     */
    public function setRenderType($renderType)
    {
        $this->renderType = $renderType;
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
