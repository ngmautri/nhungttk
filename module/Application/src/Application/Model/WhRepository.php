<?php

namespace Application\Model;
use Doctrine\ORM\EntityRepository;

/**
 *
 * @author nmt
 *        
 */
class NmtInventoryCategoryItemRepository extends EntityRepository{
	
	/**
	 * 
	 * @return array
	 */
	public function getAllWarehouse()
	{
		$sql = "
        	SELECT * 
          	FROM nmt_inventory_warehouse
   	 	";
		
		$stmt = $this->_em->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
	/**
	 *
	 * @return array
	 */
	public function getAllWarehouseOf($company_id)
	{
		$sql = "
        	SELECT *
          	FROM nmt_inventory_warehouse
			WHERE 1
   	 	";
		$sql = 	$sql. " AND nmt_inventory_warehouse.company_id = " . $company_id;
		$sql = 	$sql. ";";
		$stmt = $this->_em->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
	
}

