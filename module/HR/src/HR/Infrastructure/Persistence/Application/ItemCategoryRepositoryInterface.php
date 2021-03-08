<?php
namespace Inventory\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemCategoryRepositoryInterface
{

    public function addItemToCategory($itemId, $catId, $userId);

    public function getItemsByCategory($catId, $limit, $offset);

    public function getTotalItemsByCategory($catId);

    public function getNoneCategorizedItems($limit, $offset);

    public function getNoneCategorizedItemsTotal();
}
