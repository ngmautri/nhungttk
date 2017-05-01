<?php

namespace Workflow\Service;

use Workflow\Model\NmtWfNodeTable;

/**
 *
 * @author nmt
 *        
 */
class WorkflowService extends AbstractCategory {
	
	private $caseId;
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Workflow\Service\AbstractCategory::init()
	 */
	public function init() {
		$nodes = $this->workFlowNoteTable->fetchAll ();
		
		foreach ( $nodes as $row ) {
			$id = $row->node_id;
			$parent_id = $row->node_parent_id;
			$this->data [$id] = $row;
			$this->index [$parent_id] [] = $id;
		}
		return $this;
	}
	
	/**
	 * Fire transition
	 * 
	 * @param unknown $nodeID        	
	 */
	public function fire($nodeID) {
		$node = $this->get ( $nodeID );
		$wf_node = $node ['instance'];
		
		if ($wf_node->node_type == "TRANSITION") {
			
			echo $wf_node->node_name . " is a transition can be fired";
			echo " change Token of input place...";
			// Changing Input Place
			$wf_node_parend_id = $wf_node->node_parent_id;
			$this->changeInputPlace($wf_node_parend_id);
			
			// Changing Input Place
			$this->changeOutPlace($node);
			
		} else 
		{
			return "can not fire";
		}
	}
	
	/**
	 * 
	 * @param unknown $id
	 */
	private function changeInputPlace($id){
		
		$node = $this->get ( $id);
		$wf_node_parent = $node['instance'];
		if ($wf_node_parent->node_type == "PLACE") {
			echo $wf_node_parent->node_name . " is a INPUT PLACE...";
			// create or change token // need caseId; NodeId
			// getToken(case_id, node_id)
			// if not exist create and change.
			
			
		} else {
			echo "Not Place";
		}
	}
	
	/**
	 * 
	 * @param unknown $node: current transtion
	 */
	private function changeOutPlace($node){
		
		// Changing OutPlace
		echo " change Token of output place...";
		$node_children = $node ['children'];
		
		if (count ( $node_children ) > 0) {
			foreach ( $node_children as $child ) {
				$wf_node = $child ['instance'];
				if ($wf_node->node_type == "PLACE") {
					echo $wf_node->node_name . " is a OUT PLACE...";
					// enable transition
					$node_children_children = $child['children'];
					if(count($node_children_children>0))
					{
						foreach($node_children_children as $t){
							$wf_t = $t['instance'];
							if($wf_t->node_type="TRANSTION"){
								echo $wf_t->node_name . 'is enabled';
							}
						}
						
					}
				} else {
					echo "something wrong";
				}
			}
		}
	}
	
	/**
	 * 
	 * @param unknown $node: input place
	 */
	private function enableTransition($node){
		
	}
	
	
	// =====================================
	public function getWorkFlowNoteTable() {
		return $this->workFlowNoteTable;
	}
	public function setWorkFlowNoteTable(NmtWfNodeTable $workFlowNoteTable) {
		$this->workFlowNoteTable = $workFlowNoteTable;
		return $this;
	}
	public function getCase() {
		return $this->case;
	}
	public function setCase($case) {
		$this->case = $case;
		return $this;
	}
	public function getCaseId() {
		return $this->caseId;
	}
	public function setCaseId($caseId) {
		$this->caseId = $caseId;
		return $this;
	}
	
}