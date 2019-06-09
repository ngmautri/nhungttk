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
        if ($this->doctrineEM == null || $subject == null || $subject == "" || $this->getCompanyId() == null) {
            return false;
        }

        $criteria = array(
            "id" => $subject,
            "company" => $this->getCompanyId()
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $entiy ;
         */
        $entiy = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryItem")->findOneBy($criteria);
         return (! $entiy == null);
    }
}