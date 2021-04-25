<?php
namespace Application\Domain\Util\Pagination;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Paginator
{

    // ~ Attribute =========================================================================
    private $maxInPage = 0;

    private $minInPage = 0;

    private $maxInPageSet = 0;

    private $minInPageSet = 0;

    private $page = 1;

    private $pageSet;

    private $step = 5;

    private $pagePerPageSet = 10;

    private $resultsPerPage = 10;

    private $totalPages = 1;

    private $totalPageSets = 10;

    private $totalResults;

    // ~ Contructor ========================================================================
    function __construct($totalResults, $page, $resultsPerPage)
    {
        $this->totalResults = $totalResults;
        $this->page = $page;
        $this->resultsPerPage = $resultsPerPage;
        $this->_init();
    }

    // ~ Methods ===========================================================================

    /**
     *
     * @throws \InvalidArgumentException
     */
    private function _init()
    {
        if ($this->page <= 0 || $this->resultsPerPage <= 0) {
            throw new \InvalidArgumentException("Invalid input! Negative value not allowed");
        }

        $this->totalPages = (int) floor($this->totalResults / $this->resultsPerPage);
        if ($this->totalResults % $this->resultsPerPage > 0) {
            $this->totalPages ++;
        }

        if ($this->page > $this->totalPages) {
            throw new \InvalidArgumentException("Invalid input! Page must be smaller than total pages.");
        }

        // caculate first position in each page
        if ($this->totalPages == 1 || $this->page == 1) {
            $this->minInPage = 1;
        } else {
            $this->minInPage = ($this->page * $this->resultsPerPage) - $this->resultsPerPage + 1;
        }

        // caculate last position in each page
        if ($this->totalPages == $this->page) {
            $this->maxInPage = $this->totalResults;
        } else {
            $this->maxInPage = $this->page * $this->resultsPerPage;
        }

        // caculate first position in each page set
        $this->minInPageSet = $this->page - $this->step;

        if ($this->minInPageSet < 0 || $this->page <= 10) {
            $this->minInPageSet = 1;
        }

        // caculate last position in each page set
        $this->maxInPageSet = $this->page + $this->step;

        if ($this->maxInPageSet < 10) {
            $this->maxInPageSet = 10;
        }

        if ($this->maxInPageSet > $this->totalPages) {
            $this->maxInPageSet = $this->totalPages;
        }
    }

    public function __toString()
    {
        return '[TotalResults:' . $this->totalResults . ',TotalPages:' . $this->totalPages . ',Page:' . $this->page . ',MinInPage:' . $this->minInPage . ',MaxInPage:' . $this->maxInPage . ',MinInPageSet:' . $this->minInPageSet . ',maxInPageSet:' . $this->maxInPageSet . ']';
    }

    /**
     *
     * @return number
     */
    public function getMaxInPage()
    {
        return $this->maxInPage;
    }

    /**
     *
     * @param number $maxInPage
     */
    public function setMaxInPage($maxInPage)
    {
        $this->maxInPage = $maxInPage;
    }

    /**
     *
     * @return number
     */
    public function getMinInPage()
    {
        return $this->minInPage;
    }

    /**
     *
     * @param number $minInPage
     */
    public function setMinInPage($minInPage)
    {
        $this->minInPage = $minInPage;
    }

    /**
     *
     * @return number
     */
    public function getMaxInPageSet()
    {
        return $this->maxInPageSet;
    }

    /**
     *
     * @param number $maxInPageSet
     */
    public function setMaxInPageSet($maxInPageSet)
    {
        $this->maxInPageSet = $maxInPageSet;
    }

    /**
     *
     * @return number
     */
    public function getMinInPageSet()
    {
        return $this->minInPageSet;
    }

    /**
     *
     * @param number $minInPageSet
     */
    public function setMinInPageSet($minInPageSet)
    {
        $this->minInPageSet = $minInPageSet;
    }

    /**
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     *
     * @param
     *            Ambigous <number, unknown> $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     *
     * @return mixed
     */
    public function getPageSet()
    {
        return $this->pageSet;
    }

    /**
     *
     * @param mixed $pageSet
     */
    public function setPageSet($pageSet)
    {
        $this->pageSet = $pageSet;
    }

    /**
     *
     * @return number
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     *
     * @param number $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }

    /**
     *
     * @return number
     */
    public function getPagePerPageSet()
    {
        return $this->pagePerPageSet;
    }

    /**
     *
     * @param number $pagePerPageSet
     */
    public function setPagePerPageSet($pagePerPageSet)
    {
        $this->pagePerPageSet = $pagePerPageSet;
    }

    /**
     *
     * @return int
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     *
     * @param
     *            Ambigous <number, unknown> $resultsPerPage
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     *
     * @return number
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     *
     * @param number $totalPages
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    /**
     *
     * @return number
     */
    public function getTotalPageSets()
    {
        return $this->totalPageSets;
    }

    /**
     *
     * @param number $totalPageSets
     */
    public function setTotalPageSets($totalPageSets)
    {
        $this->totalPageSets = $totalPageSets;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalResults()
    {
        return $this->totalResults;
    }

    /**
     *
     * @param mixed $totalResults
     */
    public function setTotalResults($totalResults)
    {
        $this->totalResults = $totalResults;
    }
}