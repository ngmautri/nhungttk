<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseLocationExitsSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
       
        $warehouseId = null;
        if (isset($subject["warehouseId"])) {
            $warehouseId = $subject["warehouseId"];
        }
        
        $locationId = null;
        if (isset($subject["locationId"])) {
            $locationId = $subject["locationId"];
        }
        

        if ($this->doctrineEM == null || $warehouseId == null || $locationId == null) {
            return false;
        }

        $criteria = array(
            "id" => $locationId,
            "warehouse" => $warehouseId
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouseLocation $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryWarehouseLocation")->findOneBy($criteria);
        return (! $entity == null);
    }
}