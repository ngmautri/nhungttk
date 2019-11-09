<?php
namespace Inventory\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemCategoryRepositoryInterface
{
    public function getItemsByCategory($catId);
    public function getTotalItemsByCategory($catId);
     
}
