<?php
namespace Application\Application\Specification\InMemory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VendorExitsSpecification extends AbstractInMemorySpecification
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

        $vendorId = null;
        if (isset($subject["vendorId"])) {
            $vendorId = $subject["vendorId"];
        }

        if ($this->doctrineEM == null || $vendorId == null || $companyId == null) {
            return false;
        }

        $criteria = array(
            "id" => $vendorId
            // "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\NmtBpVendor $doctrineEntity ;
         */
        $doctrineEntity = $this->doctrineEM->getRepository("\Application\Entity\NmtBpVendor")->findOneBy($criteria);
        return (! $doctrineEntity == null);
    }
}