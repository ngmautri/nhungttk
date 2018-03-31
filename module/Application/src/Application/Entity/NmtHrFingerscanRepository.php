<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 *
 * @author nmt
 *        
 */
class NmtHrFingerscanRepository extends EntityRepository {
    
    /** @var \Application\Entity\NmtHrFingerscan $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\NmtHrFingerscanRepository")
	
	private $sql = "";

	
	/**
	 *
	 * @param number $limit        	
	 * @param number $offset        	
	 * @return array
	 */
	public function getFingerscan($employee_code=null, $day,$month,$year) {
		$sql = $this->sql;
		$s ="";
		for ($i = 1; $i <= 31; $i++){		
		    $s = $s. "sum(case when nmt_hr_fingerscan.dd ='" . $i. "-" . $month . "-" . $year .
		    "' then hour(nmt_hr_fingerscan.clock_in) else 0 end ) as hh_in_". $i . ", ";
		    
		    $s = $s. "sum(case when nmt_hr_fingerscan.dd ='" . $i. "-" . $month . "-" . $year .
		    "' then minute(nmt_hr_fingerscan.clock_in) else 0 end ) as mm_in_". $i . ", ";
		    
		    $s = $s. "sum(case when nmt_hr_fingerscan.dd ='" . $i. "-" . $month . "-" . $year .
		    "' then hour(nmt_hr_fingerscan.clock_out) else 0 end ) as hh_out_". $i . ", ";
		    
		    $s = $s. "sum(case when nmt_hr_fingerscan.dd ='" . $i. "-" . $month . "-" . $year .
		    "' then minute(nmt_hr_fingerscan.clock_out) else 0 end ) as mm_out_". $i . ", ";		   
		}
		
		$sql = " SELECT nmt_hr_fingerscan.employee_code,";
		$sql = $sql . $s;
		$sql = $sql . " nmt_hr_fingerscan.attendance_date  
        FROM nmt_hr_employee
        left join 
        (
        select
        	*,
        	concat(day(nmt_hr_fingerscan.attendance_date),'-',month(nmt_hr_fingerscan.attendance_date),'-',year(nmt_hr_fingerscan.attendance_date)) as dd
        from nmt_hr_fingerscan
        
        )
        as nmt_hr_fingerscan
        on nmt_hr_fingerscan.employee_code = nmt_hr_employee.employee_code
        where nmt_hr_employee.employee_code=2211
        group by nmt_hr_fingerscan.employee_code    
        ";
		
		//echo ($sql);
		
		$stmt = $this->_em->getConnection ()->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ();
	}
	
	
}

