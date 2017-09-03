<?php
namespace Workflow\Workflow\Procure\Factory;
use Workflow\Workflow\Procure\PrRowWorkflow;

/**
 * Concrete Factory for PR Workflow
 * @author nmt
 *        
 */
class PrRowWorkflowFactoryMLA extends PrRowWorkflowFactoryAbstract
{
    protected $subjectHandler;

   /**
    * 
    * @return \Workflow\Workflow\Procure\PrRowWorkflow
    */
    public function makePrRowWorkFlow()
    {
        // TODO Auto-generated method stub
        $wf = new PrRowWorkflow();
        $wf->setWorkflowName("PR_ROW");
        $wf->setSubject($this->getSubject());
        $wf->setSubjectHandler("procure/pr/worfklow");
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
        
        if(!isset( $workflow_list[$this->makePrRowWorkFlow()->getWorkflowName()])){
            $workflow_list[$this->makePrRowWorkFlow()->getWorkflowName()] = $this->makePrRowWorkFlow();
        }
        
        return $workflow_list;
    }
    
   
 }
