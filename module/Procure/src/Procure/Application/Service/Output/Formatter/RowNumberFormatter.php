<?php
namespace Procure\Application\Service\Output\Formatter;

use Application\Domain\Shared\Number\NumberFormatter;
use Procure\Domain\RowSnapshot;

/**
 * only for format number.
 * this is used when exporting excel or saving as pdf.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowNumberFormatter extends AbstractRowFormatter
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

        // format money
        $row->docUnitPrice = NumberFormatter::formatMoneyNumberForExcel($row->docUnitPrice, $row->getDocCurrencyISO(), $this->getLocale());
        $row->unitPrice = NumberFormatter::formatMoneyNumberForExcel($row->unitPrice, $row->getDocCurrencyISO(), $this->getLocale());
        $row->netAmount = NumberFormatter::formatMoneyNumberForExcel($row->netAmount, $row->getDocCurrencyISO(), $this->getLocale());
        $row->taxAmount = NumberFormatter::formatMoneyNumberForExcel($row->taxAmount, $row->getDocCurrencyISO(), $this->getLocale());
        $row->grossAmount = NumberFormatter::formatMoneyNumberForExcel($row->grossAmount, $row->getDocCurrencyISO(), $this->getLocale());
        $row->convertedStandardUnitPrice = NumberFormatter::formatMoneyNumberForExcel($row->convertedStandardUnitPrice, $row->getDocCurrencyISO(), $this->getLocale());
        $row->localUnitPrice = NumberFormatter::formatMoneyNumberForExcel($row->localUnitPrice, $row->getLocalCurrencyISO(), $this->getLocale());

        // format number
        $row->docQuantity = NumberFormatter::formatNumberForExcel($row->docQuantity, $this->getLocale());
        $row->convertedStandardQuantity = NumberFormatter::formatNumberForExcel($row->convertedStandardQuantity, $this->getLocale());
        $row->standardConvertFactor = NumberFormatter::formatNumberForExcel($row->standardConvertFactor, $this->getLocale());

        return $row;
    }
}
