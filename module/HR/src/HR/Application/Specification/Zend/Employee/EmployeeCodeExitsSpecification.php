<?php
namespace HR\Application\Specification\Zend\Employee;

use Application\Application\Specification\Zend\DoctrineSpecification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class EmployeeCodeExitsSpecification extends DoctrineSpecification
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

        $employeeCode = null;
        if (isset($subject["employeeCode"])) {
            $employeeCode = $subject["employeeCode"];
        }

        if ($this->doctrineEM == null || $employeeCode == null || $companyId == null) {
            return false;
        }

        $criteria = array(
            "employeeCode" => $employeeCode,
            "company" => $companyId
        );

        /**
         *
         * @var \Application\Entity\HrIndividual $entiy ;
         */
        $entiy = $this->doctrineEM->getRepository("\Application\Entity\HrIndividual")->findOneBy($criteria);
        return (! $entiy == null);
    }
}