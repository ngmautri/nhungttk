<?php
namespace Inventory\Model\GR;

use Inventory\Model\InventoryTransactionInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractTransactionStrategy implements InventoryTransactionInterface
{

    protected $contextService;

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
     * @param bool $isFlush
     */
    abstract public function doPosting($entity, $u, $isFlush = false);

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param \DateTime $reversalDate
     * @param bool $isFlush
     */
    abstract public function reverse($entity, $u, $reversalDate, $isFlush = false);
    
    /**
     * 
     *  @param array $rows
     *  @param \Application\Entity\MlaUsers $u
     *  @param bool $isFlush
     */
    abstract public function createMovement($rows, $u, $isFlush = false, $movementDate=null, $wareHouse = null);
    

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