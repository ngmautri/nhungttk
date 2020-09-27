<?php
namespace Procure\Application\Eventbus;

use Application\Domain\EventBus\Handler\Mapper\FullNameHandlerMapper;
use Application\Service\AbstractService;
use Inventory\Application\EventBus\Handler\Item\OnProcureGrPostedCreateSerialNo;
use Inventory\Application\EventBus\Handler\Transaction\OnProcureGrPostedCreateWhGr;
use Inventory\Application\EventBus\Handler\Transaction\OnProcureGrReversedCreateWhGi;
use Procure\Application\EventBus\Handler\AP\UpdateIndexOnApPosted;
use Procure\Application\EventBus\Handler\GR\OnApPostedCreateGrByWarehouse;
use Procure\Application\EventBus\Handler\GR\OnApReversedCreateGrReversalByWarehouse;
use Procure\Application\EventBus\Handler\PO\UpdateIndexOnPoPosted;
use Procure\Application\EventBus\Handler\PR\UpdateIndexOnPrSubmitted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class HandlerMapper extends AbstractService
{

    protected $queuedHandlers;

    protected $handlers;

    protected function setUpMapper()
    {
        $handlers = [
            UpdateIndexOnPrSubmitted::class,
            UpdateIndexOnPoPosted::class,
            UpdateIndexOnApPosted::class,

            OnApPostedCreateGrByWarehouse::class,
            OnApReversedCreateGrReversalByWarehouse::class,

            OnProcureGrPostedCreateWhGr::class,
            OnProcureGrPostedCreateSerialNo::class,

            OnProcureGrReversedCreateWhGi::class
        ];

        $this->handlers = $handlers;
    }

    public function getHandlerMapper()
    {
        $this->setUpMapper();
        return new FullNameHandlerMapper($this->handlers);
    }
}
