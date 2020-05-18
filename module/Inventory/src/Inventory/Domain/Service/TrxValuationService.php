<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Service\Contracts\FIFOServiceInterface;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxValuationService
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
     * @return \Inventory\Domain\Service\Contracts\FIFOServiceInterface
     */
    public function getFifoService()
    {
        return $this->fifoService;
    }
}