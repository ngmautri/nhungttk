<?php
namespace Application\Application\Specification\InMemory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseCodeExitsSpecification extends AbstractInMemorySpecification
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

        $whCode = null;
        if (isset($subject["whCode"])) {
            $whCode = $subject["whCode"];
        }

        /*
         * $warehouseId = null;
         * if (isset($subject["warehouseId"])) {
         * $warehouseId = $subject["warehouseId"];
         * }
         */
        if ($this->doctrineEM == null || $whCode == null || $companyId == null) {
            return false;
        }

        $criteria = array(
            "whCode" => $whCode,
            "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $wh ;
         */
        $wh = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryWarehouse")->findBy($criteria);

        if (count($wh) == 0)
            return false;

        return true;
    }
}