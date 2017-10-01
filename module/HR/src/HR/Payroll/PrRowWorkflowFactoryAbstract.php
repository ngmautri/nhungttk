<?php

namespace Workflow\Workflow\Procure\Factory;
use Application\Entity\NmtProcurePrRow;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Workflow\Exception\LogicException;
use Workflow\Workflow\WorkflowFactoryInterface;


/**
 * Purchase request may run through different WF
 * @author nmt
 *
 */
abstract class PrRowWorkflowFactoryAbstract implements WorkflowFactoryInterface{
    
    protected $doctrineEM;
    protected $subject;

    
    /**
     * Support Object
     * @param NmtProcurePrRow $subject
     */
    public function __construct(NmtProcurePrRow $subject)
    {
        if (! $subject instanceof NmtProcurePrRow) {
            throw new LogicException(sprintf(
                'The subject object is not an instance of Class "%s"',
                get_class(new NmtProcurePrRow())));
        }
        
        $this->subject = $subject;
    }
    
    
    abstract public function makePrRowWorkFlow();
	
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
     * @param EntityManager $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
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
