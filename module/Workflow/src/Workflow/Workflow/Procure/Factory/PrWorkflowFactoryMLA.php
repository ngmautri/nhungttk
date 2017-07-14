<?php
namespace Workflow\Workflow\Procure\Factory;
use Doctrine\ORM\EntityManager;
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
        $wf->setDoctrineEM($this->getDoctrineEM());
        return $wf->createWorkflow();
    }
}
