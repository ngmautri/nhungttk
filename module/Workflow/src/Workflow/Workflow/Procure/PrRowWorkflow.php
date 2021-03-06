<?php
namespace Workflow\Workflow\Procure;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Workflow\Workflow\AbstractWorkflow;
use Workflow\Workflow\NmtTransition;
use Workflow\Workflow\Procure\Listener\PrRowWorkflowListener;


/**
 * 
 * @author nmt
 *
 */
class PrRowWorkflow extends AbstractWorkflow
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
            'DRAFT',
            'FA',
            'PROCURE',
            'BUYING',
            'STOCK'
        ])
        ->addTransition(new NmtTransition('submit', 'DRAFT', 'FA'))
        ->addTransition(new NmtTransition('fa_yes', 'FA', 'PROCURE'))
        ->addTransition(new NmtTransition('fa_no', 'FA', 'DRAFT'))        
        ->addTransition(new NmtTransition('buy', 'PROCURE', 'BUYING'))
        ->addTransition(new NmtTransition('receive', 'BUYING', 'STOCK'));
        
        $marking = new SingleStateMarkingStore('currentState');
        
        $dispatcher = new EventDispatcher();
        $l1 = new PrRowWorkflowListener($this->doctrineEM, $this);
        
        $dispatcher->addListener('workflow.'.$this->getWorkflowName().'.guard.to_review', array(
            $l1,
            'guardReview'
        ));
        $dispatcher->addListener('workflow.'.$this->getWorkflowName().'.entered', array(
            $l1,
            'onSubmitPR'
        ));
        
        $workflow = new Workflow($definition->build(), $marking, $dispatcher, $this->getWorkflowName());
        $this->setWorkflowInstance($workflow);
        return $workflow;
    }
}
