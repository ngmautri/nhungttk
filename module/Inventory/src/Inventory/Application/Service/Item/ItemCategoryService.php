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
    
    public function getNoneCategorizedItems(){
        return $this->getItemCategoryRepository()->getNoneCategorizedItems();
    }
        
    public function addItemToCategory($itemId, $catId,$userId){
        return $this->getItemCategoryRepository()->addItemToCategory($itemId, $catId, $userId);
    }
    
    /**
     * 
     * @param int $catId
     * @return NULL|NULL[]|\Inventory\Domain\Item\ItemSnapshot[]
     */
    public function getItemsByCategory($catId){
        return $this->getItemCategoryRepository()->getItemsByCategory($catId);        
    }
    
    
    public function getTotalItemsByCategory($catId){
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
