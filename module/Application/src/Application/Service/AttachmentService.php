<?php
namespace Application\Service;
use Doctrine\ORM\EntityManager;


use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Application\Utility\Attachment\FinVInvoiceAttachmentFactory;


class AttachmentService
{

    protected $doctrineEM;
    protected $supportedSubjects = array();

   
    public function getAttachmentFactory($target)
    {
        
        switch (true) {            
            case ($target instanceof FinVendorInvoice):                
                $factory = new FinVInvoiceAttachmentFactory($target, $attachmentHeader, $attachmentFile);
                $factory->setDoctrineEM($this->doctrineEM);
                return $factory;
            
            case ($target instanceof FinVendorInvoiceRow):
                return null;
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