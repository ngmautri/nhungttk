<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Warehouse\Transaction\TransactionCmdRepositoryInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionQueryRepositoryInterface;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Warehouse\WarehouseQueryRepositoryInterface;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionPostingService
{

    protected $transactionCmdRepository;

    protected $transactionQueryRepository;

    protected $whQueryRepository;

    /**
     *
     * @param TransactionCmdRepositoryInterface $transactionCmdRepository
     * @param WarehouseQueryRepositoryInterface $whQueryRepository
     * @throws InvalidArgumentException
     */
    public function __construct(TransactionCmdRepositoryInterface $transactionCmdRepository, TransactionQueryRepositoryInterface $transactionQueryRepository, WarehouseQueryRepositoryInterface $whQueryRepository)
    {
        if ($transactionCmdRepository == null) {
            throw new InvalidArgumentException("Transaction cmd repository not set!");
        }

        if ($transactionQueryRepository == null) {
            throw new InvalidArgumentException("Transaction query repository not set!");
        }

        if ($whQueryRepository == null) {
            throw new InvalidArgumentException("Warehouse query repository not set!");
        }

        $this->transactionCmdRepository = $transactionCmdRepository;
        $this->transactionQueryRepository = $transactionCmdRepository;
        $this->whQueryRepository = $whQueryRepository;
    }

    /**
     *
     * @return \Inventory\Domain\Warehouse\Transaction\TransactionCmdRepositoryInterface
     */
    public function getTransactionCmdRepository()
    {
        return $this->transactionCmdRepository;
    }

    /**
     *
     * @return \Inventory\Domain\Warehouse\Transaction\TransactionCmdRepositoryInterface
     */
    public function getTransactionQueryRepository()
    {
        return $this->transactionQueryRepository;
    }

    /**
     *
     * @return \Inventory\Domain\Warehouse\WarehouseQueryRepositoryInterface
     */
    public function getWhQueryRepository()
    {
        return $this->whQueryRepository;
    }
}
