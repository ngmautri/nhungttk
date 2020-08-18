<?php
namespace Inventory\Domain\Transaction;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\GR\WhGrPosted;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractGoodsReceipt extends GenericTrx
{

    // Specific Attribute, if any
    // =========================

    // ==========================
    private static $instance = null;

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

            $row->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));
        }

        $this->validate($validationService, true);

        if ($this->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        /**
         *
         * @var TrxCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $snapshot = $rep->post($this, true);

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