<?php
namespace Procure\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PRListRepositoryInterface
{
    /**
     * 
     * @param int $is_active
     * @param int $pr_year
     * @param string $balance
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     */
    public function getAllPrRow($is_active = 1, $pr_year, $balance = null, $sort_by = null, $sort = "ASC", $limit, $offset);
   
    
    public function getAllPrRowTotal($is_active = 1, $pr_year, $balance = null, $sort_by = null, $sort = "ASC", $limit, $offset);
    
    
}
