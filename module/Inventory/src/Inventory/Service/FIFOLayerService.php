<?php
namespace Inventory\Service;

use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class FIFOLayerService implements EventManagerAwareInterface
{
    
    /**
     *  Valuation
     *  
     *  @param \Application\Entity\NmtInventoryItem $item
     *  @param double $issuedQuantity
     *  @param \DateTime $transactionDate
     */
    public function valuate(\Application\Entity\NmtInventoryItem $item, $issuedQuantity, $transactionDate){
        $cost =0;
        
        
        /** @todo Get Layer*/
        
        
        /** @todao Doing Valuation and update FIFO Layer*/
        
        
        return $cost;
    }
    
  
    /**
     * 
     *  @param \Application\Entity\NmtProcureGr $source
     *  @param \Application\Entity\MlaUsers $u
     *  @throws \Exception
     */
    public function createFIFOLayerFromGR(\Application\Entity\NmtProcureGr $source, \Application\Entity\MlaUsers $u)
    {
        if(!$source instanceof \Application\Entity\NmtProcureGr){
            throw new \Exception("Source object is not valid");
        }
     
        if(!$u instanceof \Application\Entity\MlaUsers){
            throw new \Exception("User is not valid");
        }
        
        //Do create 
     }
     
    /**
    * 
    *  @param \Application\Entity\FinVendorInvoice $source
    *  @param \Application\Entity\MlaUsers $u
    *  @throws \Exception
    */
     public function createFIFOLayerFromAP(\Application\Entity\FinVendorInvoice $source, \Application\Entity\MlaUsers $u)
     {
         if(!$source instanceof \Application\Entity\NmtProcureGr){
             throw new \Exception("Source object is not valid");
         }
         
         if(!$u instanceof \Application\Entity\MlaUsers){
             throw new \Exception("User is not valid");
         }
         
         //Do create
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
     * @return \Inventory\Service\ItemSearchService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

  /**
   * 
   * {@inheritDoc}
   * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
   */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(__CLASS__));
        $this->eventManager = $eventManager;
    }

}
