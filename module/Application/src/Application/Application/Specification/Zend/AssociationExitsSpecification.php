<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationExitsSpecification extends DoctrineSpecification
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

        $associationId = null;
        if (isset($subject["associationId"])) {
            $associationId = $subject["associationId"];
        }

        if ($this->doctrineEM == null || $associationId == null || $companyId == null) {
            return false;
        }

        $criteria = array(
            "id" => $associationId,
            "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryAssociation $entiy ;
         */
        $entiy = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryAssociation")->findOneBy($criteria);
        return (! $entiy == null);
    }
}