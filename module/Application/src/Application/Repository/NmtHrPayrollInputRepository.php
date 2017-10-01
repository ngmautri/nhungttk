<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author nmt
 *        
 */
class NmtHrPayrollInputRepository extends EntityRepository
{

    /** @var \Application\Entity\NmtHrPayrollInput $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\NmtHrPayrollInputRepository")
    
    private $sql = "
SELECT
	nmt_hr_payroll_input.*
FROM nmt_hr_payroll_input
JOIN
(
SELECT
	nmt_hr_payroll_input.employee_id,
	nmt_hr_payroll_input.period_id, 
	MAX(nmt_hr_payroll_input.revision_number) AS last_revision_no
FROM nmt_hr_payroll_input
Where nmt_hr_payroll_input.is_active=1
GROUP BY employee_id, period_id
)
AS nmt_hr_payroll_input1
ON nmt_hr_payroll_input.period_id = nmt_hr_payroll_input1.period_id 
AND nmt_hr_payroll_input.employee_id = nmt_hr_payroll_input1.employee_id 
AND nmt_hr_payroll_input1.last_revision_no=nmt_hr_payroll_input.revision_number

WHERE 1
";

  
    public function getLastRevisionInput($employee_id, $period_id)
    {
        $sql = $this->sql;
        
        $sql = $sql . " AND nmt_hr_payroll_input.employee_id =" . $employee_id . " AND nmt_hr_payroll_input.period_id=".$period_id;
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtHrPayrollInput', 'nmt_hr_payroll_input');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

}

