<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Inventory\Domain\Service\TransactionPostingService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GoodsReceipt extends GenericTransaction
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::post()
     */
    public function post(TransactionPostingService $postingService = null)
    {}
}