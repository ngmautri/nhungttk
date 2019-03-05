<?php
namespace Inventory\Model\Item\Prototype;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractItemPrototype
{

    protected $contextService;

    /**
     *
     * @param \Application\Entity\NmtInventoryItem $item
     * @param \Application\Entity\MlaUsers $u
     */
    abstract public function check($item, $u);
 
}