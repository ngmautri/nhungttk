<?php
namespace Inventory\Domain\Transaction;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GoodsIssue extends GenericTrx
{

    // Specific Attribute, if any
    // =========================

    // ==========================
    private static $instance = null;

    protected function afterPost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function doReverse(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function prePost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function preReserve(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function afterReserve(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function doPost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {}
}