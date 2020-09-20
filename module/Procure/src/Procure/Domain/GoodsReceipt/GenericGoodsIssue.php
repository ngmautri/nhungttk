<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class GenericGoodsIssue extends GenericGR
{

    protected function afterPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function doReverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function prePost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function preReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function afterReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function raiseEvent()
    {}

    protected function doPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}
}