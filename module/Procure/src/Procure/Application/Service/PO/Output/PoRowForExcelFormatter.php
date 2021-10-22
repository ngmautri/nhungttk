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
class PoRowForExcelFormatter extends RowFormatterDecorator
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
                $row->transactionStatus = $row->transactionStatus;
            } elseif ($row->transactionStatus == "completed") {
                $row->transactionStatus = "Done";
            }

            $row->billedAmount = NumberFormatter::formatMoneyNumberForExcel($row->billedAmount, $row->getDocCurrencyISO(), $this->getLocale());
            $row->openAPAmount = NumberFormatter::formatMoneyNumberForExcel($row->openAPAmount, $row->getDocCurrencyISO(), $this->getLocale());

            $row->draftAPQuantity = NumberFormatter::formatNumberForExcel($row->draftAPQuantity, $this->getLocale());
            $row->postedAPQuantity = NumberFormatter::formatNumberForExcel($row->draftAPQuantity, $this->getLocale());
            $row->draftGrQuantity = NumberFormatter::formatNumberForExcel($row->draftGrQuantity, $this->getLocale());
            $row->postedGrQuantity = NumberFormatter::formatNumberForExcel($row->postedGrQuantity, $this->getLocale());
            $row->confirmedGrBalance = NumberFormatter::formatNumberForExcel($row->confirmedGrBalance, $this->getLocale());
            $row->openGrBalance = NumberFormatter::formatNumberForExcel($row->confirmedGrBalance, $this->getLocale());
        }

        return $row;
    }
}
