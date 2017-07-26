<?php
namespace Workflow\Workflow;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * 
 * @author nmt
 *
 */
abstract class AbstractWorkflowListener implements EventSubscriberInterface
{
    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
    protected $doctrineEM;
    protected $workflow;

    /**
     * 
     * @param EntityManager $doctrineEM
     * @param unknown $workflow
     */
    public function __construct(EntityManager $doctrineEM, $workflow = null)
    {
        $this->doctrineEM = $doctrineEM;
        $this->workflow = $workflow;
    }

    /**
     * 
     * @return \Workflow\Workflow\AbstractWorkflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

   /**
    * 
    * @param AbstractWorkflow $workflow
    */
    public function setWorkflow(AbstractWorkflow $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * 
     * @param EntityManager $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}