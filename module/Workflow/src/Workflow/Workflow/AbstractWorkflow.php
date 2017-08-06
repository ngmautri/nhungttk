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
    protected $subjectHandler;
    protected $doctrineEM;
   
    protected $workflowName;
    protected $workflowInstance;

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
    /**
     * @return the $workflowInstance
     */
    public function getWorkflowInstance()
    {
        return $this->workflowInstance;
    }

    /**
     * @param field_type $workflowInstance
     */
    public function setWorkflowInstance($workflowInstance)
    {
        $this->workflowInstance = $workflowInstance;
    }
    /**
     * @return the $subjectHandler
     */
    public function getSubjectHandler()
    {
        return $this->subjectHandler;
    }

    /**
     * @param field_type $subjectHandler
     */
    public function setSubjectHandler($subjectHandler)
    {
        $this->subjectHandler = $subjectHandler;
    }


}
