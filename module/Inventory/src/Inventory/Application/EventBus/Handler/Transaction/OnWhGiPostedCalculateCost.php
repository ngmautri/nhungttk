<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Event\Transaction\GI\WhGiPosted;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;

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
                $cogs = $valuationSrv->calculateCOGS($rootEntity, $row);
                $row->setCalculatedCost($cogs);
            }

            $rootEntity->saveAfterCalculationCost($sharedService);

            $this->logInfo(\sprintf("COGS for WH-GI #%s caculated and saved!", $rootEntity->getId()));
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