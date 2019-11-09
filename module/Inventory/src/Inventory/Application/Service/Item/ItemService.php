<?php
namespace Inventory\Application\Service;

use Application\Service\AbstractService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemService extends AbstractService
{

    private $itemRepository;

    private $itemAssembler;

    public function getItemList()
    {}

    /**
     *
     * @return \Inventory\Domain\Item\Repository\ItemRepositoryInterface
     */
    public function getItemRepository()
    {
        return $this->itemRepository;
    }

    public function getItemAssembler()
    {
        return $this->itemAssembler;
    }

    /**
     *
     * @param \Inventory\Domain\Item\Repository\ItemRepositoryInterface $itemRepository
     */
    public function setItemRepository($itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function setItemAssembler($itemAssembler)
    {
        $this->itemAssembler = $itemAssembler;
    }
}
