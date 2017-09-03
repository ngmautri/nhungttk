<?php
namespace Workflow\Service;

use Application\Entity\NmtHrEmployee;
use Application\Entity\NmtProcurePr;
use Application\Entity\NmtProcurePrRow;
use Doctrine\ORM\EntityManager;
use Workflow\Workflow\Procure\Factory\PrWorkflowFactoryMLA;
use Workflow\Workflow\Procure\Factory\PrRowWorkflowFactoryMLA;


class WorkflowService
{

    protected $doctrineEM;
    protected $supportedSubjects = array();

    /**
     *
     * @param unknown $subject
     * @return \Workflow\Workflow\Procure\Factory\PrWorkflowFactoryMLA
     */
    public function getWorkFlowFactory($subject)
    {
        
        switch (true) {            
            case ($subject instanceof NmtProcurePr):                
                $factory = new PrWorkflowFactoryMLA($subject);
                $factory->setDoctrineEM($this->doctrineEM);
                return $factory;
            
            case ($subject instanceof NmtProcurePrRow):
                $factory = new PrRowWorkflowFactoryMLA($subject);
                $factory->setDoctrineEM($this->doctrineEM);
                return $factory;
                
            case ($subject instanceof NmtHrEmployee):
                return null;
        }
    }
    
   
    /**
     * 
     * @return array|unknown
     */
    public function getSupportedSubjects(){
        $s = new NmtProcurePr();
        $this->supportedSubjects[] = $s;
        
        $s = new NmtProcurePrRow();
        $this->supportedSubjects[] = $s;
        
        $s = new NmtHrEmployee();
        $this->supportedSubjects[] = $s;
        
        return $this->supportedSubjects;
        
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

       public function setSupportedSubjects($supportedSubjects)
    {
        $this->supportedSubjects = $supportedSubjects;
    }
}