<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\GR\WhGrPosted;
use Inventory\Domain\Event\Transaction\GR\WhOpenBalancePosted;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\GoodsReceipt;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromOpening extends GoodsReceipt implements GoodsReceiptInterface
{

    public function specify()
    {
        $this->movementType = TrxType::GR_FROM_OPENNING_BALANCE;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_IN;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GoodsReceipt::doPost()
     */
    protected function doPost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {
        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $this->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $this->markAsPosted($createdBy, $this->getPostingDate());

        foreach ($this->getDocRows() as $row) {

            $row->markAsPosted($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        }

        $this->validate($validationService);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();

        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($this, true);

        if (! $snapshot instanceof TrxSnapshot) {
            throw new RuntimeException(sprintf("Error orcured when posting WH-GR #%s", $this->getId()));
        }

        // Store document into storage.
        $target = $this;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        // Raise Event
        $event = new WhGrPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        // Raise Event
        $event = new WhOpenBalancePosted($target, $defaultParams, $params);
        $this->addEvent($event);
    }
}
