<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Contracts\AutoGeneratedDocInterface;
use Inventory\Domain\Event\Transaction\GR\WhGrPosted;
use Inventory\Domain\Exception\ValidationFailedException;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\AbstractGoodsReceipt;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Inventory\Domain\Transaction\Validator\ValidatorFactory;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\Shared\ProcureDocStatus;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRFromPurchasing extends AbstractGoodsReceipt implements GoodsReceiptInterface, AutoGeneratedDocInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::specify()
     */
    public function specify()
    {
        $this->movementType = TrxType::GR_FROM_PURCHASING;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_IN;
    }

    /**
     *
     * @param GenericGR $sourceObj
     * @param CommandOptions $options
     * @param TrxValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Transaction\GR\GRFromPurchasing
     */
    public static function postCopyFromProcureGR(GenericGR $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        Assert::isInstanceOf($sourceObj, GenericGR::class, sprintf("GRDoc Entity is required %s", "GenericGR"));
        Assert::eq($sourceObj->getDocStatus(), ProcureDocStatus::POSTED, sprintf("PO GR is not posted %s", $sourceObj->getId()));

        $rows = $sourceObj->getDocRows();
        Assert::notNull($rows, "GRDoc Entity is empty!");

        Assert::notNull($options, "Options not founds");
        Assert::notNull($sharedService, "Shared service not founds");

        /**
         *
         * @var \Inventory\Domain\Transaction\GR\GRFromPurchasing $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // Overwrite
        $instance->specify(); // important.

        $instance->setMovementDate($sourceObj->getPostingDate());
        $instance->setBaseDocId($sourceObj->getId());
        $instance->setBaseDocType($sourceObj->getDocType());

        $createdBy = $options->getUserId();
        $instance->initDoc($options);

        $instance->markAsPosted($createdBy, $sourceObj->getPostingDate());
        $instance->setRemarks($instance->getRemarks() . \sprintf('[Auto.] Ref.%s', $sourceObj->getSysNumber()));

        foreach ($rows as $r) {

            /**
             *
             * @var GRRow $r ;
             */

            if (! $r->getIsInventoryItem()) {
                continue;
            }

            $grRow = GRFromPurchasingRow::createFromPurchaseGrRow($instance, $r, $options);
            $grRow->markRowAsPosted($instance, $options);
            $instance->addRow($grRow);
        }

        $validationService = ValidatorFactory::create($instance->getMovementType(), $sharedService);
        $instance->validate($validationService);

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        $instance->clearEvents();

        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($instance, true);

        $target = $instance;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new WhGrPosted($target, $defaultParams, $params);

        $instance->addEvent($event);

        $instance->updateIdentityFrom($snapshot);
        return $instance;
    }
}