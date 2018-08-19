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
     * @return \Inventory\Service\AbstractInventoryService
     */
    public function getContextService()
    {
        return $this->contextService;
    }

    /**
     *
     * @param \Inventory\Service\AbstractInventoryService $contextService
     */
    public function setContextService(\Inventory\Service\AbstractInventoryService $contextService)
    {
        $this->contextService = $contextService;
    }
}