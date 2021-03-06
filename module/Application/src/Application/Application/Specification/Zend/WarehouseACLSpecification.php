<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseACLSpecification extends DoctrineSpecification
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

        $companyId = null;
        if (isset($subject["companyId"])) {
            $companyId = $subject["companyId"];
        }

        $userId = null;
        if (isset($subject["userId"])) {
            $userId = $subject["userId"];
        }

        if ($this->doctrineEM == null || $warehouseId == null || $companyId == null || $userId == null) {
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
        if ($wh == null) {
            return false;
        }

        if ($wh->getWhController() == null) {
            // public warehouse.
            return true;
        }

        $spec = new IsParentSpecification($this->doctrineEM);

        $subject = array(
            "userId1" => $userId,
            "userId2" => $wh->getWhController()->getId()
        );
        return $spec->isSatisfiedBy($subject);
    }
}