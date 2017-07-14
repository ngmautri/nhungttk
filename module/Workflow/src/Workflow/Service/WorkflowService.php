<?php
namespace Workflow\Service;

use Application\Entity\NmtHrEmployee;
use Application\Entity\NmtProcurePr;
use Application\Entity\NmtProcurePrRow;
use Doctrine\ORM\EntityManager;
use Procure\Workflow\PrRowWorkflow;
use Procure\Workflow\PrWorkflow;
use Symfony\Component\Workflow\Exception\LogicException;
use Workflow\Workflow\Procure\Factory\PrWorkflowFactoryMLA;

class WorkflowService
{

    protected $doctrineEM;

    protected $supportedSubjects = array();

    /**
     *
     * @deprecated
     * @param unknown $subject
     * @return \Symfony\Component\Workflow\Workflow
     */
    public function createWorkFlow($subject)
    {
        if ($subject instanceof NmtProcurePr) {
            // NmtProcurePr
            $wf = new PrWorkflow();
            $wf->setDoctrineEM($this->doctrineEM);
            return $wf->createWorkflow();
        } elseif ($subject instanceof NmtProcurePrRow) {
            $wf = new PrRowWorkflow();
            $wf->setDoctrineEM($this->doctrineEM);
            return $wf->createWorkflow();
        } elseif ($subject instanceof NmtHrEmployee) {
            $wf = new CreateEmployeeWorkflow();
            $wf->setDoctrineEM($this->doctrineEM);
            return $wf->createWorkflow();
        }
    }

    /**
     *
     * @param unknown $subject
     * @return \Symfony\Component\Workflow\Workflow
     */
    public function getPrWorkFlowFactory($subject)
    {
        if (! $subject instanceof NmtProcurePr) {
            throw new LogicException(sprintf(
                'The subject object is not an instance of Class "%s"', 
                get_class(new NmtProcurePr())));
        }
        
        // NmtProcurePr
        $factory = new PrWorkflowFactoryMLA();
        $factory->setDoctrineEM($this->doctrineEM);
        return $factory;
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
     * @return \Workflow\Service\WorkflowService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * @return the $supportedSubjects
     */
    public function getSupportedSubjects()
    {
        return $this->supportedSubjects;
    }

  
    public function setSupportedSubjects($supportedSubjects)
    {
        $this->supportedSubjects = $supportedSubjects;
    }
}