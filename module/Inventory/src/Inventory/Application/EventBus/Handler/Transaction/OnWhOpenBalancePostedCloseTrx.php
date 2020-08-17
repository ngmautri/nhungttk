<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Domain\Event\Transaction\GR\WhOpenBalancePosted;
use Inventory\Domain\Transaction\BaseRow;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhOpenBalancePostedCloseTrx extends AbstractEventHandler
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
                Throw new \InvalidArgumentException("GenericTrx not give for FIFO Layer Service!");
            }

            // close all transations.
            $rep = new TrxCmdRepositoryImpl($this->getDoctrineEM());

            $itemIds = [];
            foreach ($trx->getDocRows() as $row) {
                /**
                 *
                 * @var BaseRow $row ;
                 */
                $itemIds[] = $row->getItem;
            }

            $rep->closeTrxOf($itemIds);

            $this->logInfo(\sprintf("Transactions closed on opening balance posted!  #%s ", $trx->getId()));
        } catch (\Exception $e) {

            // There might be nothing to receive in stock, so do not throw exception, just log it.
            $this->logException($e);
        }
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return WhOpenBalancePosted::class;
    }
}