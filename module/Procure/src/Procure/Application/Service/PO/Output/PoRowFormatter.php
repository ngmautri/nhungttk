<?php
namespace Procure\Application\Service\PO\Output;

use Procure\Application\Service\Output\Formatter\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Application\Domain\Shared\Number\NumberFormatter;

/**
 * PO Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoRowFormatter extends RowFormatterDecorator
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
            $decimalNo = 4;
        }

        $row = $this->formatter->format($row);

        // then decorate
        if ($row instanceof PORowSnapshot) {
            if ($row->transactionStatus == "uncompleted") {
                $row->transactionStatus = \sprintf('&nbsp;<span  class="label label-warning"><strong>.</strong></span> %s', $row->transactionStatus);
            } elseif ($row->transactionStatus == "completed") {
                $row->transactionStatus = \sprintf('&nbsp;<span style="color: graytext;">%s</span>', "Done");
            }

            // $row->unitPrice = ($row->getUnitPrice() !== null ? number_format($row->getUnitPrice(), $decimalNo) : 0);

            $row->billedAmount = ($row->getBilledAmount() !== null ? number_format($row->getBilledAmount(), $decimalNo) : 0);
            $row->draftAPQuantity = ($row->getDraftAPQuantity() !== null ? number_format($row->getDraftAPQuantity(), $decimalNo) : 0);
            $row->openAPAmount = ($row->getOpenAPAmount() !== null ? number_format($row->getOpenAPAmount(), $decimalNo) : 0);
            $row->postedAPQuantity = ($row->getPostedAPQuantity() !== null ? number_format($row->getPostedAPQuantity(), $decimalNo) : 0);
            $row->draftGrQuantity = ($row->getDraftGrQuantity() !== null ? number_format($row->getDraftGrQuantity(), $decimalNo) : 0);
            $row->postedGrQuantity = ($row->getPostedGrQuantity() !== null ? number_format($row->getPostedGrQuantity(), $decimalNo) : 0);
            $row->confirmedGrBalance = ($row->getConfirmedGrBalance() !== null ? number_format($row->getConfirmedGrBalance(), $decimalNo) : 0);
            $row->openGrBalance = ($row->getOpenGrBalance() !== null ? number_format($row->getOpenGrBalance(), $decimalNo) : 0);
        }

        return $row;
    }
}
