<?php

namespace Application\Service;
use Application\Utility\AbstractCategory;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtApplicationDepartment;

/**
 *
 * @author nmt
 *        
 */
class ItemCategoryService extends AbstractCategory {
	
	protected $doctrineEM;
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Workflow\Service\AbstractCategory::init()
	 */
	public function initCategory() {
		$nodes = $this->getDoctrineEM()->getRepository('Application\Entity\NmtInventoryItemCategory')->findBy(array(),array('nodeName'=>'ASC'));
		
	/* 	$n = new NmtApplicationDepartment();
		$n->getNodeParentId(); */
	
		foreach ( $nodes as $row ) {
			$id = $row->getNodeId();
			$parent_id = $row->getNodeParentId();
			$this->data [$id] = $row;
			$this->index [$parent_id] [] = $id;
		}	
		return $this;
	}
	/**
	 * 
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	
	/**
	 * @Overwire
	 * {@inheritDoc}
	 * @see \Application\Utility\AbstractCategory::generateJSTree()
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
	
	/**
	 * 
	 * @param EntityManager $doctrineEM
	 * @return \Application\Service\DepartmentService
	 */
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	
	
	
	
}