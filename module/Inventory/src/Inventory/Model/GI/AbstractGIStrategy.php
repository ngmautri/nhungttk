<?php
namespace Inventory\Model\GI;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractGIStrategy
{

    protected $contextService;
    
    
    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $entity
     */
    abstract public function validateRow($entity);
       
    

    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\NmtInventoryItem $item
     * @param \Application\Entity\MlaUsers $u
     */
    abstract public function check($trx, $item, $u);

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     */
    abstract public function doPosting($entity, $u);

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param \DateTime $reversalDate
     */
    abstract public function reverse($entity, $u, $reversalDate);

   /**
    * 
    *  @return \Application\Service\AbstractService
    */
    public function getContextService()
    {
        return $this->contextService;
    }

   /**
    * 
    *  @param \Application\Service\AbstractService $contextService
    */
    public function setContextService(\Application\Service\AbstractService $contextService)
    {
        $this->contextService = $contextService;
    }
}