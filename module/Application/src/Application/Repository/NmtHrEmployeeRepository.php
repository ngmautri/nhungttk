<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class NmtHrEmployeeRepository extends EntityRepository
{

    /** @var \Application\Entity\NmtHrEmployee $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\NmtHrEmployeeRepository")
    
    /**
     *
     * @param number $contract_id
     * @param string $token
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getNoneComponentOfContract($contract_id, $token)
    {
        $sql_tmp = "
SELECT
*
FROM nmt_hr_salary_default
WHERE nmt_hr_salary_default.is_active=1 AND nmt_hr_salary_default.id NOT IN
(
	SELECT nmt_hr_salary.default_salary_id AS id FROM nmt_hr_salary
	WHERE nmt_hr_salary.contract_id=%s)";
        
        $sql = sprintf($sql_tmp, $contract_id,$token);
     
        //echo $sql;
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtHrSalaryDefault', 'nmt_hr_salary_default');
             $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}

