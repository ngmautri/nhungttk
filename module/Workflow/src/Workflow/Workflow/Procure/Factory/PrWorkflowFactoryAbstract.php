<?php

namespace Workflow\Workflow\Procure\Factory;
use Application\Entity\NmtProcurePr;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Workflow\Exception\LogicException;
use Workflow\Workflow\WorkflowFactoryInterface;


/**
 * Purchase request may run through different WF
 * @author nmt
 *
 */
abstract class PrWorkflowFactoryAbstract implements WorkflowFactoryInterface{
    
    protected $doctrineEM;
    protected $subject;    
    
    /**
     * Support Object
     * @param NmtProcurePr $subject
     */
    public function __construct(NmtProcurePr $subject)
    {
        if (! $subject instanceof NmtProcurePr) {
            throw new LogicException(sprintf(
                'The subject object is not an instance of Class "%s"',
                get_class(new NmtProcurePr())));
        }
        
        $this->subject = $subject;
    }
    
    
    abstract public function makePrSendingWorkflow();
	
	// Adding more if needed
	
	
	
	//==========================================
    /**
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * @return the $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * 
     * @param unknown $doctrineEM
     */
    public function setDoctrineEM($doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    /**
     * @param \Application\Entity\NmtProcurePr $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

	
	
}
