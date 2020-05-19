<?php
namespace Application\Application\Specification\Zend;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DepartmentExitsSpecification extends DoctrineSpecification
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

        $departmentId = null;
        if (isset($subject["departmentId"])) {
            $departmentId = $subject["departmentId"];
        }

        if ($this->doctrineEM == null || $companyId == null || $departmentId == null) {
            return false;
        }

        $criteria = array(
            "nodeId" => $departmentId,
            "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\NmtApplicationDepartment $doctrineEntity ;
         */
        $doctrineEntity = $this->doctrineEM->getRepository("\Application\Entity\NmtApplicationDepartment")->findOneBy($criteria);
        return (! $doctrineEntity == null);
    }
}