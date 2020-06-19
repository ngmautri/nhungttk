<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationItemExitsSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $associationId = null;
        if (isset($subject["associationId"])) {
            $associationId = $subject["associationId"];
        }

        $mainItemId = null;
        if (isset($subject["mainItemId"])) {
            $mainItemId = $subject["mainItemId"];
        }

        $relatedItemId = null;
        if (isset($subject["relatedItemId"])) {
            $relatedItemId = $subject["relatedItemId"];
        }

        if ($this->doctrineEM == null || $associationId == null || $relatedItemId == null || $mainItemId == null) {
            return false;
        }

        $criteria = array(
            "association" => $associationId,
            "mainItem" => $mainItemId,
            "relatedItem" => $relatedItemId
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryAssociationItem $entiy ;
         */
        $entiy = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryAssociationItem")->findOneBy($criteria);
        return (! $entiy == null);
    }
}