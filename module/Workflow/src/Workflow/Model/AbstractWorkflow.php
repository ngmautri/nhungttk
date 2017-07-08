<?php

namespace Workflow\Model;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;

/**
 * 
 * @author nmt
 *
 */
abstract class AbstractWorkflow {
    
	abstract public function createWorkflow();
	
	abstract public function getWorkflowName();
	
	abstract public function setWorkflowName($name);
}
