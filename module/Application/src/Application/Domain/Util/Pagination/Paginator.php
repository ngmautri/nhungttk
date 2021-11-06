<?php
namespace Application\Domain\Util\Pagination;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Paginator
{

    /*
     * |=============================
     * | Input
     * |
     * |=============================
     */
    private $totalResults;

    private $page = 1;

    private $resultsPerPage = 10;

    private $pagePerPageSet = 10;

    private $step = 5;

    /*
     * |=============================
     * | to calculate
     * |
     * |=============================
     */
    private $totalPages;

    private $minInPage = 0;

    private $maxInPage = 0;

    private $minInPageSet = 0;

    private $maxInPageSet = 0;

    /*
     * |=============================
     * | OTher
     * |
     * |=============================
     */
    private $pageSet;

    private $totalPageSets = 20;

    /*
     * |=============================
     * | For printing
     * |
     * |=============================
     */
    private $baseUrl;

    private $urlConnectorSymbol;

    private $displayHTMLDiv;

    /*
     * |=============================
     * |Constructor
     * |
     * |=============================
     */
    function __construct($totalResults, $page = null, $resultsPerPage = null)
    {
        if ($page == null) {
            $page = 1;
        }

        if ($resultsPerPage == null) {
            $resultsPerPage = 10;
        }

        $this->totalResults = $totalResults;
        $this->page = $page;
        $this->resultsPerPage = $resultsPerPage;
        $this->_init();
    }

    /*
     * |=============================
     * |Methods
     * |
     * |=============================
     */

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
        $f = 'Total result: %s, total pages:%s, $result per page:%s, $result per pageset:%s';
        return '[TotalResults:' . $this->totalResults . ',TotalPages:' . $this->totalPages . ',Page:' . $this->page . ',MinInPage:' . $this->minInPage . ',MaxInPage:' . $this->maxInPage . ',MinInPageSet:' . $this->minInPageSet . ',maxInPageSet:' . $this->maxInPageSet . ']';
    }

    /*
     * |=============================
     * |Render
     * |
     * |=============================
     */
    public function printPaginator()
    {
        return PaginatorRender::createPaginator($this, $this->getBaseUrl(), $this->getUrlConnectorSymbol());
    }

    public function printAjaxPaginator()
    {
        return PaginatorRender::createPaginatorAjax($this, $this->getBaseUrl(), $this->getUrlConnectorSymbol(), $this->getDisplayHTMLDiv());
    }

    public function printCustomPaginator($baseUrl, $connector_symbol)
    {
        return PaginatorRender::createPaginator($this, $baseUrl, $connector_symbol);
    }

    public function printCustomAjaxPaginator($baseUrl, $connector_symbol, $result_div)
    {
        return PaginatorRender::createPaginatorAjax($this, $baseUrl, $connector_symbol, $result_div);
    }

    /*
     * |=============================
     * |Offset and limit
     * |
     * |=============================
     */
    public function getLimit()
    {
        if ($this->getTotalPages() == 1) {
            return null;
        }
        return ($this->getMaxInPage() - $this->getMinInPage()) + 1;
    }

    public function getOffset()
    {
        if ($this->getTotalPages() == 1) {
            return null;
        }
        return $this->getMinInPage() - 1;
    }

    /*
     * |=============================
     * |Getter and Setter
     * |
     * |=============================
     */
    /**
     *
     * @return mixed
     */
    public function getTotalResults()
    {
        return $this->totalResults;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
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
     * @return number
     */
    public function getStep()
    {
        return $this->step;
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
     * @return number
     */
    public function getMaxInPage()
    {
        return $this->maxInPage;
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
     * @return number
     */
    public function getMaxInPageSet()
    {
        return $this->maxInPageSet;
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
     * @return mixed
     */
    public function getPageSet()
    {
        return $this->pageSet;
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
     * @param mixed $totalResults
     */
    public function setTotalResults($totalResults)
    {
        $this->totalResults = $totalResults;
    }

    /**
     *
     * @param
     *            Ambigous <number, string> $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     *
     * @param
     *            Ambigous <number, string> $resultsPerPage
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;
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
     * @param number $step
     */
    public function setStep($step)
    {
        $this->step = $step;
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
     * @param number $maxInPage
     */
    public function setMaxInPage($maxInPage)
    {
        $this->maxInPage = $maxInPage;
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
     * @param number $maxInPageSet
     */
    public function setMaxInPageSet($maxInPageSet)
    {
        $this->maxInPageSet = $maxInPageSet;
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
     * @param mixed $pageSet
     */
    public function setPageSet($pageSet)
    {
        $this->pageSet = $pageSet;
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
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     *
     * @return mixed
     */
    public function getUrlConnectorSymbol()
    {
        return $this->urlConnectorSymbol;
    }

    /**
     *
     * @return mixed
     */
    public function getDisplayHTMLDiv()
    {
        return $this->displayHTMLDiv;
    }

    /**
     *
     * @param mixed $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     *
     * @param mixed $urlConnectorSymbol
     */
    public function setUrlConnectorSymbol($urlConnectorSymbol)
    {
        $this->urlConnectorSymbol = $urlConnectorSymbol;
    }

    /**
     *
     * @param mixed $displayHTMLDiv
     */
    public function setDisplayHTMLDiv($displayHTMLDiv)
    {
        $this->displayHTMLDiv = $displayHTMLDiv;
    }
}