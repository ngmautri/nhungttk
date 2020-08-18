<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Inventory\Application\Service\Item\SerialNoServiceImpl;
use Inventory\Domain\Event\Transaction\GR\WhGrPosted;
use Inventory\Domain\Event\Transaction\GR\WhOpenBalancePosted;
use Inventory\Domain\Transaction\GenericTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhOpenBlancePostedCreateSerialNo extends AbstractEventHandler
{

    /**
     *
     * @param WhGrPosted $event
     */
    public function __invoke(WhOpenBalancePosted $event)
    {
        try {

            $trx = $event->getTarget();
            if (! $trx instanceof GenericTrx) {
                Throw new \InvalidArgumentException("GenericTrx not give for FIFO Layer Service!");
            }

            $sv = new SerialNoServiceImpl();
            $sv->setDoctrineEM($this->getDoctrineEM());
            // $sv->createSerialNoFor($event->getTarget());

            $this->logInfo(\sprintf("Serial No for PO-GR#%s handled and created, if any!", $$trx->getId()));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function priority()
    {
        return 30;
    }

    public static function subscribedTo()
    {
        return WhOpenBalancePosted::class;
    }
}