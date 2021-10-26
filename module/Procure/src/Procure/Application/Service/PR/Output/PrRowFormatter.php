<?php
namespace Procure\Application\Service\PR\Output;

use Application\Domain\Shared\Number\NumberFormatter;
use Procure\Application\Service\Output\Formatter\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\Contracts\ProcureTrxStatus;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 * RR Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowFormatter extends RowFormatterDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Formatter\AbstractRowFormatter::format()
     */
    public function format(RowSnapshot $row)
    {
        if (! $row instanceof RowSnapshot) {
            return null;
        }

        // using same local
        $this->formatter->setLocale($this->getLocale());

        $row = $this->formatter->format($row);

        if ($row instanceof PRRowSnapshot) {

            switch ($row->getTransactionStatus()) {
                case ProcureTrxStatus::UNCOMPLETED:
                    $row->transactionStatus = \sprintf('&nbsp;<span  class="label label-warning">.</span><span style="font-weight: normal;"> %s</span>', 'pending');
                    break;

                case ProcureTrxStatus::HAS_QUOTATION:
                    $row->transactionStatus = \sprintf('&nbsp;<span style="color:navy;">%s</span> ', "Quoted");
                    break;

                case ProcureTrxStatus::COMPLETED:
                    $row->transactionStatus = \sprintf('&nbsp;<span style="color:graytext;">%s</span> ', "Done");
                    break;
                case ProcureTrxStatus::COMMITTED:
                    $row->transactionStatus = \sprintf('&nbsp;<span style="color:green;">%s</span> ', "Committed");
                    break;
                case ProcureTrxStatus::PARTIAL_COMMITTED:
                    $row->transactionStatus = \sprintf('&nbsp;<span style="color:green;">%s</span> ', "Parial committed");
                    break;
            }

            $row->convertedStandardQuantity = NumberFormatter::formatNumberForGrid($row->getConvertedStandardQuantity(), $this->getLocale());

            $row->standardQoQuantity = NumberFormatter::formatNumberForGrid($row->getStandardQoQuantity(), $this->getLocale());
            $row->postedStandardQoQuantity = NumberFormatter::formatNumberForGrid($row->getPostedStandardQoQuantity(), $this->getLocale());

            $row->standardPoQuantity = NumberFormatter::formatNumberForGrid($row->getStandardPoQuantity(), $this->getLocale());
            $row->postedPoQuantity = NumberFormatter::formatNumberForGrid($row->getPostedPoQuantity(), $this->getLocale());
            $row->postedStandardPoQuantity = NumberFormatter::formatNumberForGrid($row->getPostedStandardPoQuantity(), $this->getLocale());

            $row->postedGrQuantity = NumberFormatter::formatNumberForGrid($row->getPostedGrQuantity(), $this->getLocale());
            $row->postedStandardGrQuantity = NumberFormatter::formatNumberForGrid($row->getPostedStandardPoQuantity(), $this->getLocale());

            $row->postedGrQuantity = NumberFormatter::formatNumberForGrid($row->getPostedGrQuantity(), $this->getLocale());
            $row->postedStandardGrQuantity = NumberFormatter::formatNumberForGrid($row->getPostedStandardGrQuantity(), $this->getLocale());

            $row->postedStockQrQuantity = NumberFormatter::formatNumberForGrid($row->getPostedStockQrQuantity(), $this->getLocale());
            $row->postedStandardStockQrQuantity = NumberFormatter::formatNumberForGrid($row->getPostedStandardStockQrQuantity(), $this->getLocale());

            $row->postedApQuantity = NumberFormatter::formatNumberForGrid($row->getPostedApQuantity(), $this->getLocale());
            $row->postedStandardApQuantity = NumberFormatter::formatNumberForGrid($row->getPostedStandardApQuantity(), $this->getLocale());

            $row->lastStandardUnitPrice = NumberFormatter::formatMoneyNumberForGrid($row->getLastStandardUnitPrice(), $row->getLastCurrency(), $this->getLocale());
        }

        return $row;
    }
}
