<?php
namespace MLA;

/**
 *
 * @author Ngmautri
 *
 */
class Paginator
{
	// ~ Attribute =========================================================================
	public $maxInPage = 0;
	public $minInPage = 0;

	public $maxInPageSet = 0;
	public $minInPageSet = 0;

	public $page = 1;
	public $pageSet;
	public $step=5;
	public $pagePerPageSet = 10;
	public $resultsPerPage = 10;
	public $totalPages = 1;
	public $totalPageSets = 10;
	public $totalResults;

	// ~ Contructor ========================================================================
	function __construct($totalResults, $page, $resultsPerPage){
		$this->totalResults = $totalResults;
		$this->page = $page;
		$this->resultsPerPage = $resultsPerPage;
		$this->_init();
	}

	// ~ Methods ===========================================================================

	/**
	 * Intial caculation
	 * @return unknown_type
	 */
	private function _init()
	{
		if($this->page <= 0 || $this->resultsPerPage <= 0)
		{
			throw new Exception("Invalid input! Negative value not allowed");				
		}

		$this->totalPages = (int) floor($this->totalResults / $this->resultsPerPage);
		if ($this->totalResults % $this->resultsPerPage > 0)
		{
			$this->totalPages++;
		}
	
		if($this->page > $this->totalPages)
		{
			throw new Exception("Invalid input! Page must be smaller than total pages.");
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

		if($this->minInPageSet<0 || $this->page <= 10)
		{
			$this->minInPageSet = 1;
		}

		// caculate last position in each page set
		$this->maxInPageSet = $this->page + $this->step;

		if($this->maxInPageSet < 10){
			$this->maxInPageSet = 10;
		}

		if($this->maxInPageSet > $this->totalPages)
		{
			$this->maxInPageSet = $this->totalPages;
		}
	}

	public function __toString()
	{
		return '[TotalResults:'.$this->totalResults.',TotalPages:'.$this->totalPages.',Page:'.$this->page.',MinInPage:'.
		$this->minInPage.',MaxInPage:'.$this->maxInPage.
		',MinInPageSet:'.$this->minInPageSet.',maxInPageSet:'.$this->maxInPageSet. ']';		
	}
}