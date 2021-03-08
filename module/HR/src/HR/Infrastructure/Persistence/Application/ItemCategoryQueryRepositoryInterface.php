<?php
namespace Inventory\Infrastructure\Persistence;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemCategoryQueryRepositoryInterface
{

    public function getCategory($catId);

    public function getRoot($rootId);

    public function getCategoryByName($catName);
}
