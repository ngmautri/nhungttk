<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CompanyUserExitsSpecification extends DoctrineSpecification
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

        $userId = null;
        if (isset($subject["userId"])) {
            $userId = $subject["userId"];
        }

        if ($this->doctrineEM == null || $userId == null || $companyId == null) {
            return false;
        }

        $criteria = array(
            "id" => $userId,
            "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\MlaUsers $entiy ;
         */
        $entiy = $this->doctrineEM->getRepository("\Application\Entity\MlaUsers")->findOneBy($criteria);
        return (! $entiy == null);
    }
}