<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Domain\Event\Transaction\GR\WhOpenBalancePosted;
use Inventory\Domain\Transaction\GenericTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhOpenBalancePostedCloseFifoLayer extends AbstractEventHandler
{

    /**
     *
     * @param WhOpenBalancePosted $event
     * @throws \InvalidArgumentException
     */
    public function __invoke(WhOpenBalancePosted $event)
    {
        try {

            $trx = $event->getTarget();
            if (! $trx instanceof GenericTrx) {
                Throw new \InvalidArgumentException("GenericTrx not give for FIFO Layer Service! OnWhOpenBalancePostedCloseFifoLayer");
            }

            // close all fifo current fifo layer.
            $fifoService = new FIFOServiceImpl();
            $fifoService->setDoctrineEM($this->getDoctrineEM());

            $fifoService->closeLayersOf($trx);

            $this->logInfo(\sprintf("Fifo layer closed on opening balance posted!  #%s ", $trx->getId()));
        } catch (\Exception $e) {

            // There might be nothing to receive in stock, so do not throw exception, just log it.
            $this->logException($e);
        }
    }

    public static function priority()
    {
        return 10;
    }

    public static function subscribedTo()
    {
        return WhOpenBalancePosted::class;
    }
}