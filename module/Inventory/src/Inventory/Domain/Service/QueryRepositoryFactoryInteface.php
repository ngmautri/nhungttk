<?php
namespace Inventory\Domain\Service;

/**
 * Inventory Repository Factory
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface QueryRepositoryFactoryInteface
{

    public function createItemQueryRepository();

    public function createWarehouseQueryRepository();

    public function createTransactionQueryRepository();
}
