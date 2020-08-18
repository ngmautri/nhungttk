<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\GR\WhOpenBalancePosted;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\AbstractGoodsReceipt;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromOpening extends AbstractGoodsReceipt implements GoodsReceiptInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\AbstractGoodsReceipt::prePost()
     */
    protected function prePost(\Application\Domain\Shared\Command\CommandOptions $options, \Inventory\Domain\Service\Contracts\TrxValidationServiceInterface $validationService, \Inventory\Domain\Service\SharedService $sharedService)
    {
        // return GenericTrx
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

    protected function afterPost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {
        // raise event, if needed.
    }

    public function specify()
    {
        $this->movementType = TrxType::GR_FROM_OPENNING_BALANCE;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_IN;
    }
}
