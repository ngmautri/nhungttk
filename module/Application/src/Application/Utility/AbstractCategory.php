<?php

namespace Application\Utility;
/**
 * 
 * @author Nguyen Mau Tri
 *
 */
abstract class AbstractCategory {
	
	protected $categories = array ();
	protected $data = array ();
	protected $index = array ();
	protected $jsTree;
	
	
	
	abstract protected function initCategory();
	
	
	/**
	 * 
	 * @param unknown $index
	 * @param unknown $data
	 * @param unknown $parent_id
	 * @param unknown $level
	 */
	public function updateCategory($parent_id, $level)
	{
	
		if (isset($this->index[$parent_id])) {
			foreach ($this->index[$parent_id] as $id) {
	
				//pre-order travesal
				$this->updateCategory($id, $level+1);
	
				if(isset($this->data[$parent_id])):
				//echo $level . "." . str_repeat(" - ", $level) .$data[$id]->role . "==". $data[$id]->path . "\n";
	
				//$roleName = ucwords($this->data[$id]->role);
				//$parentRoleName = ucwords($this->data[$parent_id]->role);
	
				try	{
					if(!$this->has($id))
					{
						$this->add($id,$parent_id);
					}else{
						$this->updateParent($id,$parent_id);
					}
	
				}catch(\Exception $e )
				{
					//var_dump($e);
				}
				//echo $level . ". " . str_repeat(" - ", $level) . $parentRoleName .'//'.$roleName . "==". $data[$id]->path . "\n";
				endif;
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @param unknown $parent_id
	 * @return unknown[]
	 */
	public function getChildNodes($parent_id){
		$children = array();
		if (isset($this->index[$parent_id])) {
			foreach ($this->index[$parent_id] as $id) {
				$children[] = $id;
				$children = array_merge($children, $this->getChildNodes($id));
			}
		}
		return $children;
	}
	
	
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
					'instance' =>  $this->data[$parent],
					// 'parents' => array(),
					'children' => array ()
			);
		}
	
		// $catParent = $this->get ( $parent );
		// var_dump($catParent['children']);
		$this->categories [$cat] = array (
				'instance' => $this->data[$cat],
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
					'instance' => $this->data[$parent],
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
	
	/**
	 * 
	 * @param unknown $root
	 * @return string|unknown
	 */
	public function generateJSTree($root) {
		$tree = $this->categories [$root];
		$children = $tree ['children'];
	
		//inorder travesal	
		if (count ( $children ) > 0) {
			$this->jsTree = $this->jsTree . '<li data-jstree=\'{ "opened" : true}\'> ' . ucwords($tree['instance']->getNodeName()) . '('.count ( $children ).")\n";
			$this->jsTree = $this->jsTree . '<ul>' . "\n";
			foreach ( $children as $key => $value ) {
				
				if (count ( $value ['children'] ) > 0) {
					$this->generateJSTree ($key);
				} else {
					$this->jsTree = $this->jsTree . '<li data-jstree=\'{}\'>' . $value ['instance']->getNodeName() . ' </li>' . "\n";
					$this->generateJSTree ($key);
				}
			}
			$this->jsTree = $this->jsTree . '</ul>' . "\n";
				
			$this->jsTree = $this->jsTree . '</li>' . "\n";
		}
		
		return $this->jsTree;
	}
	
	// Getter Setter
	
	public function getCategories() {
		return $this->categories;
	}
	public function setCategories($categories) {
		$this->categories = $categories;
		return $this;
	}
	public function getData() {
		return $this->data;
	}
	public function setData($data) {
		$this->data = $data;
		return $this;
	}
	public function getIndex() {
		return $this->index;
	}
	public function setIndex($index) {
		$this->index = $index;
		return $this;
	}
	public function getJsTree() {
		return $this->jsTree;
	}
	public function setJsTree($jsTree) {
		$this->jsTree = $jsTree;
		return $this;
	}
	
	
	
}
