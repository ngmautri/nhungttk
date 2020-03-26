<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Exception\InvalidArgumentException;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionValuationService
{

    protected $fifoService;

    public function __construct(FIFOServiceInterface $fifoService)
    {
        if ($fifoService == null) {
            throw new InvalidArgumentException("Fifo Service not found");
        }

        $this->fifoService = $fifoService;
    }

    /**
     *
     * @return \Inventory\Domain\Service\FIFOServiceInterface
     */
    public function getFifoService()
    {
        return $this->fifoService;
    }
}