<?php
namespace Inventory\Model;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractTransactionStrategy implements InventoryTransactionInterface
{

    protected $contextService;

    abstract public function check($trx, $item, $u);
    
    
    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $entity
     * @param array $data
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isNew
     * @param boolean $isPosting
     */
    abstract public function validateRow($entity,$data,$u,$isNew, $isPosting);

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
     * @param array $rows
     * @param \Application\Entity\MlaUsers $u
     * @param boolean $isFlush
     * @param string $movementDate
     * @param object $wareHouse
     * @param string $trigger
     */
    abstract public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null, $trigger=null);

    /**
     *
     * @return \Application\Service\AbstractService
     */
    public function getContextService()
    {
        return $this->contextService;
    }

    /**
     *
     * @param \Application\Service\AbstractService $contextService
     */
    public function setContextService(\Application\Service\AbstractService $contextService)
    {
        $this->contextService = $contextService;
    }
}