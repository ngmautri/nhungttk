<?php
namespace Inventory\Model\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractItemStrategy
{

    protected $contextService;

    /**
     *
     * @param \Application\Entity\NmtInventoryItem $item
     * @param \Application\Entity\MlaUsers $u
     */
    abstract public function check($item, $u);

    /**
     *
     * @param \Application\Entity\NmtInventoryItem $entity
     * @param \Application\Entity\MlaUsers $u
     */
    abstract public function create($entity, $u);

    /**
     *
     * @param \Application\Entity\NmtInventoryItem $entity
     * @param \Application\Entity\MlaUsers $u
     * @param \DateTime $reversalDate
     */
    abstract public function edit($entity, $u, $reversalDate);

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