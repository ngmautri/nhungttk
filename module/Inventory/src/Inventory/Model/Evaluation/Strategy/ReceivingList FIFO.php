<?php
namespace Inventory\Model\Valuation\Strategy;

use SplQueue;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ReceivingListFIFO extends SplQueue implements InterfaceReceivingList
{

    /**
     *
     * {@inheritdoc}
     * @see SplQueue::add()
     */
    public function add($itemGR)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\Valuation\Strategy\InterfaceReceivingList::remove()
     */
    public function remove($itemGR)
    {}
}