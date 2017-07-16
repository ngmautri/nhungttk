<?php
namespace Workflow\Workflow;

use Doctrine\ORM\EntityManager;

/**
 *
 * @author nmt
 *        
 */
abstract class AbstractWorkflow
{
    protected $workflowFactory;
    protected $subject;
    protected $doctrineEM;
    protected $workflowName;

    abstract public function createWorkflow();

    /**
     * 
     * @return field_type
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     *
     * @return the $workflowFactory
     */
    public function getWorkflowFactory()
    {
        return $this->workflowFactory;
    }

    /**
     *
     * @param field_type $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     *
     * @param field_type $workflowFactory
     */
    public function setWorkflowFactory($workflowFactory)
    {
        $this->workflowFactory = $workflowFactory;
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

    /**
     *
     * @return the $workflowName
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     *
     * @param field_type $workflowName
     */
    public function setWorkflowName($workflowName)
    {
        $this->workflowName = $workflowName;
    }
}
