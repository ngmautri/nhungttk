<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Application\Event\DefaultParameter;
use Inventory\Domain\Event\Transaction\GR\WhGrPosted;
use Inventory\Domain\Event\Transaction\GR\WhOpenBalancePosted;
use Inventory\Domain\Transaction\GoodsReceipt;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromOpening extends GoodsReceipt implements GoodsReceiptInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GoodsReceipt::doPost()
     */
    protected function doPost(\Application\Domain\Shared\Command\CommandOptions $options, \Inventory\Domain\Service\Contracts\TrxValidationServiceInterface $validationService, \Inventory\Domain\Service\SharedService $sharedService)
    {
        // Store document into storage.

        // Raise Event
        $target = $this;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $event = new WhGrPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        // Raise Event
        $target = $this;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $event = new WhOpenBalancePosted($target, $defaultParams, $params);
        $this->addEvent($event);
    }

    public function __construct()
    {
        $this->movementType = TrxType::GR_FROM_OPENNING_BALANCE;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_IN;
    }
}
