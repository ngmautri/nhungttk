<?php

namespace Workflow\Service;

use Workflow\Model\NmtWfNodeTable;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author nmt
 *        
 */
class WorkflowService extends AbstractCategory {
	
	protected $doctrineEM;
	private $caseId;
	
	public function purchaseWF(){
		
		$factory = new \Petrinet\Model\Factory();
		$builder = new \Petrinet\Builder\PetrinetBuilder($factory);
		$petrinet = $builder
		->connect($builder->place(), $t1 = $builder->transition())
		->connect($t1, $p2 = $builder->place())
		->connect($t1, $p3 = $builder->place())
		->connect($p2, $t2 = $builder->transition())
		->connect($p3, $t2)
		->connect($t2, $builder->place())
		->getPetrinet();
	
		
		// Instanciates the Dumper
		//$dumper = new \Petrinet\Dumper\GraphvizDumper();
		
		// Dumps the Petrinet structure
		//$string = $dumper->dump($petrinet);
		return ($petrinet);
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Workflow\Service\AbstractCategory::init()
	 */
	public function init() {
		$nodes= $this->doctrineEM->getRepository ( 'Application\Entity\NmtWfNode' )->findAll ();
		
		
		//$nodes = $this->workFlowNoteTable->fetchAll ();
		
		foreach ( $nodes as $row ) {
			$id = $row->getNodeId();
			$parent_id = $row->getNodeParentId();
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
		
		if ($wf_node->getNodeType() == "TRANSITION") {
			
			printf ($wf_node->getNodeName(). " is a transition can be fired\n");
			printf (" change Token of input place...\n");
			// Changing Input Place
			$wf_node_parend_id = $wf_node->getNodeParentId();
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
		if ($wf_node_parent->getNodeType()== "PLACE") {
			printf($wf_node_parent->getNodeName(). " is a INPUT PLACE...\n");
			/** @todo create or change token // need caseId; NodeId */
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
		printf (" change Token of output place...\n");
		$node_children = $node ['children'];
		
		if (count ( $node_children ) > 0) {
			foreach ( $node_children as $child ) {
				$wf_node = $child ['instance'];
				if ($wf_node->getNodeType()== "PLACE") {
					printf($wf_node->getNodeName() . " is a OUTPUT PLACE...\n");
					// enable transition
					$node_children_children = $child['children'];
					if(count($node_children_children>0))
					{
						foreach($node_children_children as $t){
							$wf_t = $t['instance'];
							if($wf_t->getNodeType()=="TRANSTION"){
								printf($wf_t->getNodeName(). ' is enabled\n');
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
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	
	
}