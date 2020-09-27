<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Event\Transaction\GI\WhGiPosted;
use Inventory\Domain\Event\Transaction\GI\WhGiforPoReturnPosted;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnWhGiForPoReturnPostedCalculateCost extends AbstractEventHandler
{

    /**
     *
     * @param WhGiPosted $event
     */
    public function __invoke(WhGiforPoReturnPosted $event)
    {
        try {

            $target = $event->getTarget();

            if (! $target instanceof GenericTrx) {
                Throw new \InvalidArgumentException("GenericTrx not found! OnWhGiForPoReturnPostedCalculateCost");
            }

            $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
            $rootEntity = $rep->getById($target->getId());
            $sharedService = SharedServiceFactory::createForTrx($this->getDoctrineEM(), $this->getLogger());
            $rows = $rootEntity->getDocRows();

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
                $cogs = $valuationSrv->calculateCostForReturn($rootEntity, $row);
                $row->setCalculatedCost($cogs);
            }

            $rootEntity->saveAfterCalculationCost($sharedService);

            $this->logInfo(\sprintf("COGS for WH-GI for PO Return#%s caculated and saved!", $rootEntity->getId()));
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
        return WhGiforPoReturnPosted::class;
    }
}