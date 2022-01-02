<?php
namespace Procure\Application\Service\PR\RowCollectionRender\Formatter;

use Application\Domain\Shared\Number\NumberFormatter;
use Application\Domain\Util\Collection\Formatter\FormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\Contracts\ProcureTrxStatus;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 * RR Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowFormatter extends FormatterDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\ElementFormatterInterface::format()
     */
    public function format($element)
    {
        if (! $element instanceof RowSnapshot) {
            return null;
        }

        // using same local
        $this->formatter->setLocale($this->getLocale());

        $element = $this->formatter->format($element);

        if ($element instanceof PRRowSnapshot) {

            switch ($element->getTransactionStatus()) {
                case ProcureTrxStatus::UNCOMPLETED:
                    $element->transactionStatus = \sprintf('&nbsp;<span  class="label label-warning">.</span><span style="font-weight: normal;"> %s</span>', 'pending');
                    break;

                case ProcureTrxStatus::HAS_QUOTATION:
                    $element->transactionStatus = \sprintf('&nbsp;<span style="color:navy;">%s</span> ', "Quoted");
                    break;

                case ProcureTrxStatus::COMPLETED:
                    $element->transactionStatus = \sprintf('&nbsp;<span style="color:graytext;">%s</span> ', "Done");
                    break;
                case ProcureTrxStatus::COMMITTED:
                    $element->transactionStatus = \sprintf('&nbsp;<span style="color:green;">%s</span> ', "Committed");
                    break;
                case ProcureTrxStatus::PARTIAL_COMMITTED:
                    $element->transactionStatus = \sprintf('&nbsp;<span style="color:green;">%s</span> ', "Parial committed");
                    break;
            }

            $element->convertedStandardQuantity = NumberFormatter::formatNumberForGrid($element->getConvertedStandardQuantity(), $this->getLocale());

            $element->standardQoQuantity = NumberFormatter::formatNumberForGrid($element->getStandardQoQuantity(), $this->getLocale());
            $element->postedStandardQoQuantity = NumberFormatter::formatNumberForGrid($element->getPostedStandardQoQuantity(), $this->getLocale());

            $element->standardPoQuantity = NumberFormatter::formatNumberForGrid($element->getStandardPoQuantity(), $this->getLocale());
            $element->postedPoQuantity = NumberFormatter::formatNumberForGrid($element->getPostedPoQuantity(), $this->getLocale());
            $element->postedStandardPoQuantity = NumberFormatter::formatNumberForGrid($element->getPostedStandardPoQuantity(), $this->getLocale());

            $element->postedGrQuantity = NumberFormatter::formatNumberForGrid($element->getPostedGrQuantity(), $this->getLocale());
            $element->postedStandardGrQuantity = NumberFormatter::formatNumberForGrid($element->getPostedStandardPoQuantity(), $this->getLocale());

            $element->postedGrQuantity = NumberFormatter::formatNumberForGrid($element->getPostedGrQuantity(), $this->getLocale());
            $element->postedStandardGrQuantity = NumberFormatter::formatNumberForGrid($element->getPostedStandardGrQuantity(), $this->getLocale());

            $element->postedStockQrQuantity = NumberFormatter::formatNumberForGrid($element->getPostedStockQrQuantity(), $this->getLocale());
            $element->postedStandardStockQrQuantity = NumberFormatter::formatNumberForGrid($element->getPostedStandardStockQrQuantity(), $this->getLocale());

            $element->postedApQuantity = NumberFormatter::formatNumberForGrid($element->getPostedApQuantity(), $this->getLocale());
            $element->postedStandardApQuantity = NumberFormatter::formatNumberForGrid($element->getPostedStandardApQuantity(), $this->getLocale());

            $element->lastStandardUnitPrice = NumberFormatter::formatMoneyNumberForGrid($element->getLastStandardUnitPrice(), $element->getLastCurrency(), $this->getLocale());
        }

        return $element;
    }
}
