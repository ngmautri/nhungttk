<?php
namespace Inventory\Application\Service\Item;

use Application\Service\AbstractService;
use Inventory\Infrastructure\Persistence\Doctrine\ItemCategoryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCategoryService extends AbstractService
{

    private $itemCategoryRepository;

    /**
     *
     * @param int $limit
     * @param int $offset
     * @return NULL|NULL[]|\Inventory\Domain\Item\ItemSnapshot[]
     */
    public function getNoneCategorizedItems($limit, $offset)
    {
        return $this->getItemCategoryRepository()->getNoneCategorizedItems($limit, $offset);
    }

    public function getNoneCategorizedItemsTotal()
    {
        return $this->getItemCategoryRepository()->getNoneCategorizedItemsTotal();
    }

    public function addItemToCategory($itemId, $catId, $userId)
    {
        return $this->getItemCategoryRepository()->addItemToCategory($itemId, $catId, $userId);
    }

    /**
     *
     * @param int $catId
     * @return NULL|NULL[]|\Inventory\Domain\Item\ItemSnapshot[]
     */
    public function getItemsByCategory($catId, $limit, $offset)
    {
        return $this->getItemCategoryRepository()->getItemsByCategory($catId, $limit, $offset);
    }

    public function getTotalItemsByCategory($catId)
    {
        return $this->getItemCategoryRepository()->getTotalItemsByCategory($catId);
    }

    /**
     *
     * @return \Inventory\Infrastructure\Persistence\Doctrine\ItemCategoryRepositoryImpl
     */
    public function getItemCategoryRepository()
    {
        return $this->itemCategoryRepository;
    }

    /**
     *
     * @param ItemCategoryRepositoryImpl $itemCategoryRepository
     */
    public function setItemCategoryRepository(ItemCategoryRepositoryImpl $itemCategoryRepository)
    {
        $this->itemCategoryRepository = $itemCategoryRepository;
    }
}
