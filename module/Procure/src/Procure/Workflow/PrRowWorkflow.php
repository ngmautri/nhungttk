<?php
namespace Procure\Workflow;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Workflow\Model\AbstractWorkflow;

use Procure\Workflow\Listener\PrRowWorkflowListener;

/**
 *
 * @author nmt
 *        
 */
class PrRowWorkflow extends AbstractWorkflow
{
    protected $doctrineEM;
    protected $workflowName;

    /**
     * {@inheritDoc}
     * @see \Workflow\Model\AbstractWorkflow::getWorkflowName()
     */
    public function getWorkflowName()
    {
        // TODO Auto-generated method stub
        return $this->workflowName;
    }

    /**
     * {@inheritDoc}
     * @see \Workflow\Model\AbstractWorkflow::setWorkflowName()
     */
    public function setWorkflowName($name)
    {
        // TODO Auto-generated method stub
        $this->workflowName = $name;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Workflow\Model\AbstractWorkflow::createWorkflow()
     */
    public function createWorkflow()
    {
        // TODO Auto-generated method stub
        $definition = new DefinitionBuilder();
        // Transitions are defined with a unique name, an origin place and a destination place
        
        $definition->addPlaces([
            'draft',
            'department_head',
            'department_head_approved',
            'department_head_rejected',
            'fa_budget_check',
            'fa_budget_check_approved',
            'fa_budget_check_rejected',
            'procurement',
            'bought',
            'delivered'
        ])
            ->addTransition(new Transition('submit_to_department_head', 'draft', 'department_head'))
            ->addTransition(new Transition('department_head_yes', 'department_head', 'fa_budget_check'))
            ->addTransition(new Transition('department_head_no', 'department_head', 'department_head_rejected'))
            ->addTransition(new Transition('fa_yes', 'fa_budget_check', 'procurement'))
            ->addTransition(new Transition('fa_no', 'fa_budget_check', 'fa_budget_check_rejected'))
            ->addTransition(new Transition('buy', 'procurement', 'bought'))
            ->addTransition(new Transition('delivery', 'bought', 'delivered'));
        
        $marking = new SingleStateMarkingStore('currentState');
    
        $dispatcher = new EventDispatcher();
        $l1 = new PrRowWorkflowListener($this->doctrineEM);
    
        $dispatcher->addListener('workflow.PR_ROW_WORKFLOW.guard.to_review', array(
            $l1,
            'guardReview'
        ));
        $dispatcher->addListener('workflow.PR_ROW_WORKFLOW.entered', array(
            $l1,
            'onSubmitPR'
        ));
        
      /*   $l2 = new WorkflowLogger();
        $dispatcher->addListener('workflow.leave', array(
            $l2,
            'onLeave'
        )); */
        
        $workflow = new Workflow($definition->build(), $marking, $dispatcher, "PR_ROW_WORKFLOW");
         
         /** @todo */
         $l1->setWorkflow($workflow);
         
        return $workflow;
    }
    
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
