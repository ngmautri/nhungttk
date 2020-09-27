<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Event\Transaction\GI\WhGiPosted;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use Inventory\Domain\Transaction\GenericTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnWhGiPostedCalculateCost extends AbstractEventHandler
{

    /**
     *
     * @param WhGiPosted $event
     */
    public function __invoke(WhGiPosted $event)
    {
        try {

            $target = $event->getTarget();

            if (! $target instanceof GenericTrx) {
                Throw new \InvalidArgumentException("GenericTrx not found! OnWhGiPostedCalculateCost");
            }

            $sharedService = SharedServiceFactory::createForTrx($this->getDoctrineEM(), $this->getLogger());
            $rows = $target->getDocRows();

            if ($rows == null) {
                Throw new \RuntimeException("Doc is empty");
            }

            foreach ($rows as $row) {

                /**
                 *
                 * @var TrxRow $row ;
                 */

                if ($row->getDocQuantity() == 0) {
                    continue;
                }

                // caculate COGS
                $valuationSrv = $sharedService->getValuationService()->getFifoService();
                $cogs = $valuationSrv->calculateCOGS($target, $row);
                $row->setCalculatedCost($cogs);
            }

            $target->saveAfterCalculationCost($sharedService);

            $this->logInfo(\sprintf("COGS for WH-GI #%s caculated and saved!", $target->getId()));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return WhGiPosted::class;
    }
}