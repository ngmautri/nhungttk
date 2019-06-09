<?php
namespace Application\Application\Specification\Zend;



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
        if ($this->doctrineEM == null || $subject == null || $subject == "" || $this->getCompanyId() == null) {
            return false;
        }
        
        $criteria = array(
            "id" => $subject,
            "company" => $this->getCompanyId()
        );
        
        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $wh ;
         */
        $wh = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryWarehouse")->findOneBy($criteria);
        return (! $wh == null);
    }
}