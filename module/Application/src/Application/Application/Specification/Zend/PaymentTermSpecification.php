<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PaymentTermSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $paymentTermId = null;
        if (isset($subject["paymentTermId"])) {
            $paymentTermId = $subject["paymentTermId"];
        }

        if ($this->doctrineEM == null || $paymentTermId == null) {
            return false;
        }

        $criteria = array(
            "id" => $paymentTermId
        );

        /**
         *
         * @var \Application\Entity\NmtApplicationPmtTerm $doctrineEntity ;
         */
        $doctrineEntity = $this->doctrineEM->getRepository("\Application\Entity\NmtApplicationPmtTerm")->findOneBy($criteria);
        return (! $doctrineEntity == null);
    }
}