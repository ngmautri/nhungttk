<?php
namespace Inventory\Application\Service;

use Application\Service\AbstractService;
use Inventory\Domain\Item\Repository\ItemRepositoryInterface;
use Inventory\Application\DTO\ItemAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemService extends AbstractService
{

    /**
     *
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     *
     * @var ItemAssembler
     */
    private $itemAssembler;

    public function getItemList()
    {
        
    }

    /**
     *
     * @return \Inventory\Domain\Item\Repository\ItemRepositoryInterface
     */
    public function getItemRepository()
    {
        return $this->itemRepository;
    }

    /**
     *
     * @return \Inventory\Application\DTO\ItemAssembler
     */
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

    /**
     *
     * @param \Inventory\Application\DTO\ItemAssembler $itemAssembler
     */
    public function setItemAssembler($itemAssembler)
    {
        $this->itemAssembler = $itemAssembler;
    }
}
