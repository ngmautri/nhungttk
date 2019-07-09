<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemExitsSpecification extends DoctrineSpecification
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

        $itemId = null;
        if (isset($subject["itemId"])) {
            $itemId = $subject["itemId"];
        }

        if ($this->doctrineEM == null || $itemId == null || $companyId == null) {
            return false;
        }

        $criteria = array(
            "id" => $subject,
            "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $entiy ;
         */
        $entiy = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryItem")->findOneBy($criteria);
        return (! $entiy == null);
    }
}