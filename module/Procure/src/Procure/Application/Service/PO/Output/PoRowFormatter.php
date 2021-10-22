<?php
namespace Procure\Application\Service\PO\Output;

use Application\Domain\Shared\Number\NumberFormatter;
use Procure\Application\Service\Output\Formatter\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshot;

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

        // using same local
        $this->formatter->setLocale($this->getLocale());
        $row = $this->formatter->format($row);

        // then decorate
        if ($row instanceof PORowSnapshot) {
            if ($row->transactionStatus == "uncompleted") {
                $row->transactionStatus = \sprintf('&nbsp;<span  class="label label-warning"><strong>.</strong></span> %s', $row->transactionStatus);
            } elseif ($row->transactionStatus == "completed") {
                $row->transactionStatus = \sprintf('&nbsp;<span style="color: graytext;">%s</span>', "Done");
            }

            $row->billedAmount = NumberFormatter::formatMoneyNumberForGrid($row->billedAmount, $row->getDocCurrencyISO(), $this->getLocale());
            $row->openAPAmount = NumberFormatter::formatMoneyNumberForGrid($row->openAPAmount, $row->getDocCurrencyISO(), $this->getLocale());

            $row->draftAPQuantity = NumberFormatter::formatNumberForGrid($row->draftAPQuantity, $this->getLocale());
            $row->postedAPQuantity = NumberFormatter::formatNumberForGrid($row->draftAPQuantity, $this->getLocale());
            $row->draftGrQuantity = NumberFormatter::formatNumberForGrid($row->draftGrQuantity, $this->getLocale());
            $row->postedGrQuantity = NumberFormatter::formatNumberForGrid($row->postedGrQuantity, $this->getLocale());
            $row->confirmedGrBalance = NumberFormatter::formatNumberForGrid($row->confirmedGrBalance, $this->getLocale());
            $row->openGrBalance = NumberFormatter::formatNumberForGrid($row->confirmedGrBalance, $this->getLocale());
        }

        return $row;
    }
}
