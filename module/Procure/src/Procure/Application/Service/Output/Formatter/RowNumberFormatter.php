<?php
namespace Procure\Application\Service\Output\Formatter;

use Procure\Domain\RowSnapshot;
use Application\Domain\Shared\Number\NumberFormatter;

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

        $decimalNo = 0;
        $curency = array(
            "USD",
            "THB",
            "EUR",
            "DKK"
        );

        if (in_array($row->getDocCurrencyISO(), $curency)) {
            $decimalNo = 2;
        }

        if ($row->convertedStandardUnitPrice !== null) {
            $row->convertedStandardUnitPrice = number_format($row->convertedStandardUnitPrice, $decimalNo);
        }

        if ($row->docQuantity != null) {
            $row->docQuantity = NumberFormatter::format($row->docQuantity, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->standardConvertFactor != null) {
            $row->standardConvertFactor = NumberFormatter::format($row->standardConvertFactor, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->exwUnitPrice != null) {
            $row->exwUnitPrice = NumberFormatter::format($row->exwUnitPrice, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->docUnitPrice != null) {
            $row->docUnitPrice = NumberFormatter::format($row->docUnitPrice, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->unitPrice != null) {
            $row->unitPrice = NumberFormatter::format($row->unitPrice, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->netAmount != null) {
            $row->netAmount = NumberFormatter::format($row->netAmount, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->taxAmount != null) {
            $row->taxAmount = NumberFormatter::format($row->taxAmount, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->grossAmount != null) {
            $row->grossAmount = NumberFormatter::format($row->grossAmount, $this->getLocale(), $decimalNo, $decimalNo);
        }

        return $row;
    }
}
