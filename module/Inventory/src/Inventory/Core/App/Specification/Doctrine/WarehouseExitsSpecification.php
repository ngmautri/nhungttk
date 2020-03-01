<?php
namespace Inventory\Application\Specification\Doctrine;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseExitsSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        if ($this->doctrineEM == null || $subject == null || $subject == "")
            return false;

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $wh ;
         */
        $wh = $this->doctrineEM->find("\Application\Entity\NmtInventoryWarehouse", $subject);
        return (! $wh == null);
    }
}