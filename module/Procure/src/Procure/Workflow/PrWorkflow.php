<?php
namespace Procure\Workflow;

use Doctrine\ORM\EntityManager;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Workflow\Model\AbstractWorkflow;

use Procure\Workflow\Listener\PrWorkflowListener;

/**
 *
 * @author nmt
 *        
 */
class PrWorkflow extends AbstractWorkflow
{

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Workflow\Model\AbstractWorkflow::getWorkflowName()
     */
    public function getWorkflowName()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Workflow\Model\AbstractWorkflow::setWorkflowName()
     */
    public function setWorkflowName($name)
    {
        // TODO Auto-generated method stub
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
            'sent',
            'received',
            'recalled'
        ])
            ->addTransition(new Transition('send', 'draft', 'sent'))
            ->addTransition(new Transition('get', 'sent', 'received'))
            ->addTransition(new Transition('recall', 'received', 'recalled'))
            ->addTransition(new Transition('resend', 'recalled', 'sent'));
        
        $marking = new SingleStateMarkingStore('currentState');
        
        $dispatcher = new EventDispatcher();
        $l1 = new PrWorkflowListener($this->doctrineEM);
        
        $dispatcher->addListener('workflow.PR_WORKFLOW.guard.to_review', array(
            $l1,
            'guardReview'
        ));
        $dispatcher->addListener('workflow.PR_WORKFLOW.entered', array(
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
        
        $workflow = new Workflow($definition->build(), $marking, $dispatcher, "PR_WORKFLOW");
        
        /**
         * @todo
         */
        $l1->setWorkflow($workflow);
        
        return $workflow;
    }

    /**
     *
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param field_type $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
