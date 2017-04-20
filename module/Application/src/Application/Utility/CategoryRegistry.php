<?php

namespace Application\Utility;

class CategoryRegistry {
	
	public $categories = array ();
	
	/**
	 *
	 * @param unknown $cat        	
	 * @param unknown $parent        	
	 * @throws \Exception
	 */
	public function add($cat, $parent = null) {
		if ($this->has ( $cat )) {
			throw new \Exception ( sprintf ( 'Category id "%s" already exists in the registry', $cat ) );
		}
		
		// var_dump($this->has($parent));
		if (! $this->has ( $parent )) {
			// add parent
			$this->categories [$parent] = array (
					'instance' => $parent,
					// 'parents' => array(),
					'children' => array () 
			);
		}
		
		// $catParent = $this->get ( $parent );
		// var_dump($catParent['children']);
		$this->categories [$cat] = array (
				'instance' => $cat,
				// 'parents' => array ($parent) ,
				'children' => array () 
		);
		
		$this->categories [$parent] ['children'] [$cat] = $this->categories [$cat];
		
		return $this;
	}
	
	/**
	 *
	 * @param unknown $cat        	
	 * @param unknown $parent        	
	 */
	public function updateParent($cat, $parent = null) {
		
		// var_dump($this->has($parent));
		if (! $this->has ( $parent )) {
			// add parent
			$this->categories [$parent] = array (
					'instance' => $parent,
					// 'parents' => array(),
					'children' => array () 
			);
		}
		
		// $catParent = $this->get ( $parent );
		// var_dump($catParent['children']);
		// $this->categories[$cat]['parent'][]=$parent;
		
		$this->categories [$parent] ['children'] [$cat] = $this->categories [$cat];
		
		return $this;
	}
	
	/**
	 *
	 * @param unknown $cat        	
	 */
	public function get($cat) {
		return $this->categories [$cat];
	}
	
	/**
	 *
	 * @param unknown $cat        	
	 */
	public function has($cat) {
		return isset ( $this->categories [$cat] );
	}
	public function getCategories() {
		return $this->categories;
	}
	public function setCategories($categories) {
		$this->categories = $categories;
		return $this;
	}
}
