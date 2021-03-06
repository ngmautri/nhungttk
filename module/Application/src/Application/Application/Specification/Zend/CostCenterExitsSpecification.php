<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CostCenterExitsSpecification extends DoctrineSpecification
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

        $costCenter = null;
        if (isset($subject["costCenter"])) {
            $costCenter = $subject["costCenter"];
        }

        if ($this->doctrineEM == null || $costCenter == null || $companyId == null) {
            return false;
        }

        $criteria = array(
            "id" => $costCenter,
            "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\FinCostCenter $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\FinCostCenter")->findOneBy($criteria);
        return (! $entity == null);
    }
}