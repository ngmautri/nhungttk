<?php
namespace Workflow\Service;

use Application\Entity\NmtHrEmployee;
use Application\Entity\NmtProcurePr;
use Doctrine\ORM\EntityManager;
use HR\Workflow\CreateEmployeeWorkflow;
use Procure\Workflow\PrRowWorkflow;
use Procure\Workflow\PrWorkflow;


 /**
  * 
  * @author nmt
  *
  */
class WorkflowService
{

    protected $doctrineEM;

    /**
     *
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
}