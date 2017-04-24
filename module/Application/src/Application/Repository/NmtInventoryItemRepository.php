<?php

namespace Application\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * 
 * @author nmt
 *        
 */
class NmtInventoryItemRepository extends EntityRepository{
	//@ORM\Entity(repositoryClass="Application\Repository\NmtInventoryItemRepository")
	/**
	 * 
	 * @return array
	 */
	public function getAllItem()
	{
		$sql = "
        	SELECT * 
          	FROM nmt_inventory_Item
			Where 1 AND nmt_inventory_Item.is_sparepart=1
   	 	";
		
		$stmt = $this->_em->getConnection()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}

