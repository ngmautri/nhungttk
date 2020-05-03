<?php
namespace Inventory\Domain\Transaction;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Contracts\PostingServiceInterface;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;

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

    protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {}

    protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {}

    protected function afterPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {}

    protected function prePost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {}

    protected function preReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {}

    protected function afterReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {}
}