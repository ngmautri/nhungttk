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
abstract class AbstractGoodsIssue extends GenericTrx
{

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

            // caculate COGS
            $cogs = $sharedService->getValuationService()
                ->getFifoService()
                ->calculateCOGS($this, $row);
            $row->setCalculatedCost($cogs);
            $row->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));
        }

        $this->validate($validationService->getHeaderValidators(), $validationService->getRowValidators(), true);

        if ($this->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($this, true);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::afterPost()
     */
    protected function afterPost(\Application\Domain\Shared\Command\CommandOptions $options, \Inventory\Domain\Service\Contracts\TrxValidationServiceInterface $validationService, \Inventory\Domain\Service\SharedService $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::afterReserve()
     */
    protected function afterReserve(\Application\Domain\Shared\Command\CommandOptions $options, \Inventory\Domain\Service\Contracts\TrxValidationServiceInterface $validationService, \Inventory\Domain\Service\SharedService $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::doReverse()
     */
    protected function doReverse(\Application\Domain\Shared\Command\CommandOptions $options, \Inventory\Domain\Service\Contracts\TrxValidationServiceInterface $validationService, \Inventory\Domain\Service\SharedService $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::prePost()
     */
    protected function prePost(\Application\Domain\Shared\Command\CommandOptions $options, \Inventory\Domain\Service\Contracts\TrxValidationServiceInterface $validationService, \Inventory\Domain\Service\SharedService $sharedService)
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::preReserve()
     */
    protected function preReserve(\Application\Domain\Shared\Command\CommandOptions $options, \Inventory\Domain\Service\Contracts\TrxValidationServiceInterface $validationService, \Inventory\Domain\Service\SharedService $sharedService)
    {
        // TODO Auto-generated method stub
    }
}