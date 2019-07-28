<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseCodeExitsSpecification extends DoctrineSpecification
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
        $wh = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryWarehouse")->findOneBy($criteria);
        return (! $wh == null);
    }
}