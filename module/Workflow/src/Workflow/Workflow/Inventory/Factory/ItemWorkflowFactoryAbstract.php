<?php

namespace Workflow\Workflow\Procure\Factory;
use Doctrine\ORM\EntityManager;


/**
 * Purchase request may run through different WF
 * @author nmt
 *
 */
abstract class ItemWorkflowFactoryAbstract {
    
    protected $doctrineEM;
    
    /**
     * Make Workflow for Sending PR
     */
	abstract public function makeItemCreateWorkflow();
	
	
    /**
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * @param field_type $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

}
