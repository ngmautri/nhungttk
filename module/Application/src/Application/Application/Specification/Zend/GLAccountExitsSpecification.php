<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GLAccountExitsSpecification extends DoctrineSpecification
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

        $glAccountId = null;
        if (isset($subject["glAccountId"])) {
            $glAccountId = $subject["glAccountId"];
        }

        if ($this->doctrineEM == null || $glAccountId == null || $companyId == null) {
            return false;
        }

        $criteria = array(
            "id" => $glAccountId,
            "company" => $companyId
        );
  
        /**
         *
         * @var \Application\Entity\FinAccount $entiy ;
         */
        $entiy = $this->doctrineEM->getRepository("\Application\Entity\FinAccount")->findOneBy($criteria);
        return (! $entiy == null);
    }
}