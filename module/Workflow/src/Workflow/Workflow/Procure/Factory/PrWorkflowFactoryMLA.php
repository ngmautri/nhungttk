<?php
namespace Workflow\Workflow\Procure\Factory;
use Workflow\Workflow\Procure\PrWorkflow;

/**
 * Concrete Factory for PR Workflow
 * @author nmt
 *        
 */
class PrWorkflowFactoryMLA extends PrWorkflowFactoryAbstract
{

    /**
     *
     * {@inheritdoc}
     * @see \Workflow\Workflow\Procure\Factory\PrWorkflowFactoryAbstract::makePrSendingWorkflow()
     */
    public function makePrSendingWorkflow()
    {
        // TODO Auto-generated method stub
        $wf = new PrWorkflow();
        $wf->setWorkflowName("PR_SUBMIT");
        $wf->setSubject($this->getSubject());
        $wf->setWorkflowFactory($this);
        $wf->setDoctrineEM($this->getDoctrineEM());
        return $wf;
    }
    
   /**
    * 
    * @return \Workflow\Workflow\Procure\PrWorkflow[]
    */
    public function getWorkFlowList()
    {
        $workflow_list= array();
        
        if(!isset( $workflow_list[$this->makePrSendingWorkflow()->getWorkflowName()])){
            $workflow_list[$this->makePrSendingWorkflow()->getWorkflowName()] = $this->makePrSendingWorkflow();
        }
        
        return $workflow_list;
    }

}
