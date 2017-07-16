<?php
namespace Workflow\Workflow\Procure;

use Procure\Workflow\Listener\PrWorkflowListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Workflow\Workflow\AbstractWorkflow;
use Workflow\Workflow\NmtTransition;

class PrWorkflow extends AbstractWorkflow
{

    protected $doctrineEM;
    
   /**
    * 
    * {@inheritDoc}
    * @see \Workflow\Workflow\AbstractWorkflow::createWorkflow()
    */
    public function createWorkflow()
    {
        // TODO Auto-generated method stub
        $definition = new DefinitionBuilder();
        // Transitions are defined with a unique name, an origin place and a destination place
        
        $definition->addPlaces([
            'draft',
            'sent',
            'received',
            'recalled'
        ])
            ->addTransition(new NmtTransition('send', 'draft', 'sent'))
            ->addTransition(new NmtTransition('get', 'sent', 'received'))
            ->addTransition(new NmtTransition('recall', 'received', 'recalled'))
            ->addTransition(new NmtTransition('resend', 'recalled', 'sent'));
        
        $marking = new SingleStateMarkingStore('currentState');
        
        $dispatcher = new EventDispatcher();
        $l1 = new PrWorkflowListener($this->doctrineEM);
        
        $dispatcher->addListener('workflow.'.$this->getWorkflowName().'.guard.to_review', array(
            $l1,
            'guardReview'
        ));
        $dispatcher->addListener('workflow.'.$this->getWorkflowName().'.entered', array(
            $l1,
            'onSubmitPR'
        ));
        
        /*
         * $l2 = new WorkflowLogger();
         * $dispatcher->addListener('workflow.leave', array(
         * $l2,
         * 'onLeave'
         * ));
         */
        
        $workflow = new Workflow($definition->build(), $marking, $dispatcher, $this->getWorkflowName());
        
        /**
         *
         * @todo
         */
        $l1->setWorkflow($workflow);
        
        return $workflow;
    }
}
