<?php
namespace Inventory\Model;

/**
 * Inventory Transaction
 *
 * @author Nguyen Mau Tri
 *        
 */
interface InventoryTransactionInterface
{

    public function getTransactionIdentifer();

    public function getFlow();
}

