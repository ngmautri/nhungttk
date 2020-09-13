<?php
namespace Inventory\Domain\Transaction\GI;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\GI\WhDisposalPosted;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIForDisposal extends GenericTrx
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::specify()
     */
    public function specify()
    {
        $this->movementType = TrxType::GI_FOR_DISPOSAL;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_OUT;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::doPost()
     */
    protected function doPost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {
        /**
         *
         * @var TrxRow $row ;
         */
        $postedDate = new \Datetime();

        $this->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }
            // no need to calcuate price.
            $row->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));
        }

        $this->validate($validationService, true);

        if ($this->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        /**
         *
         * @var TrxCmdRepositoryInterface $rep ;
         * @var TrxSnapshot $snapshot ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $snapshot = $rep->post($this, true);
        $this->updateIdentityFrom($snapshot);

        $target = $this;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new WhDisposalPosted($target, $defaultParams, $params);
        $this->addEvent($event);
    }

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
}