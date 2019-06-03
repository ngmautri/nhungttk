<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CompanyExitsSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        if ($this->doctrineEM == null || $subject == null || $subject == "")
            return false;

        /**
         *
         * @var \Application\Entity\NmtApplicationCompany $entity ;
         */
        $entity = $this->doctrineEM->find("\Application\Entity\NmtApplicationCompany", $subject);
        return (! $entity == null);
    }
}