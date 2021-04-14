<?php
namespace Application\Application\Specification\InMemory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseExitsSpecification extends AbstractInMemorySpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $companyId = null;
        if (isset($subject["companyId"])) {
            $companyId = $subject["companyId"];
        }

        $warehouseId = null;
        if (isset($subject["warehouseId"])) {
            $warehouseId = $subject["warehouseId"];
        }

        if ($this->doctrineEM == null || $warehouseId == null || $companyId == null) {
            return false;
        }

        $criteria = array(
            "id" => $warehouseId,
            "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $wh ;
         */
        $wh = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryWarehouse")->findOneBy($criteria);
        return (! $wh == null);
    }
}