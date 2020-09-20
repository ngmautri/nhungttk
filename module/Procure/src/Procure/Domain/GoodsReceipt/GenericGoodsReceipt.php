<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\GoodsReceipt\Repository\GrCmdRepositoryInterface;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericGoodsReceipt extends GenericGR
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::doPost()
     */
    protected function doPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        /**
         *
         * @var GRRow $row ;
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
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        /**
         *
         * @var GRSnapshot $rootSnapshot
         * @var GrCmdRepositoryInterface $rep
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->post($this);
    }

    protected function afterPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function doReverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function prePost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function preReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    public function specify()
    {}

    protected function afterReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function raiseEvent()
    {}
}