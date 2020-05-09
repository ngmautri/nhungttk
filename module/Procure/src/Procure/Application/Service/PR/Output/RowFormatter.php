<?php
namespace Procure\Application\Service\PR\Output;

use Procure\Application\Service\Output\Formatter\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 * RR Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowFormatter extends RowFormatterDecorator
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

        $decimalNo = 0;
        $curency = array(
            "USD",
            "THB",
            "EUR"
        );

        if (in_array($row->getDocCurrencyISO(), $curency)) {
            $decimalNo = 2;
        }

        $row = $this->formatter->format($row);

        $zero = \sprintf('<span style="color:white;">0</span>');
        // $zero = 0;
        // then decorate
        if ($row instanceof PRRowSnapshot) {

            if ($row->transactionStatus == "uncompleted") {
                $row->transactionStatus = \sprintf('&nbsp;<span  class="label label-warning"><strong>.</strong></span> %s', $row->transactionStatus);
            } elseif ($row->transactionStatus == "completed") {
                $row->transactionStatus = \sprintf('&nbsp;<span style="color:graytext;">%s</span> ', "Done");
            }

            $row->postedPoQuantity = ($row->getPostedPoQuantity() > 0 ? number_format($row->getPostedPoQuantity(), 0) : $zero);
            $row->postedGrQuantity = ($row->getPostedGrQuantity() > 0 ? number_format($row->getPostedGrQuantity(), 0) : $zero);
            $row->postedStockQrQuantity = ($row->getPostedStockQrQuantity() > 0 ? number_format($row->getPostedStockQrQuantity(), 0) : $zero);
            $row->postedApQuantity = ($row->getPostedApQuantity() > 0 ? number_format($row->getPostedApQuantity(), 0) : $zero);
        }

        return $row;
    }
}
