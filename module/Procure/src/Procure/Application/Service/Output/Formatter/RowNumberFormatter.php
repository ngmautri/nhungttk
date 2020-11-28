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

        if ($row->docUnitPrice !== null) {
            // $row->docUnitPrice = number_format($row->docUnitPrice, $decimalNo);
            $row->docUnitPrice = NumberFormatter::format($row->docUnitPrice, $this->getLocale());
        }

        if ($row->netAmount !== null) {
            // / $row->netAmount = number_format($row->netAmount, $decimalNo);
            $row->netAmount = NumberFormatter::format($row->netAmount, $this->getLocale());
        }

        if ($row->taxAmount !== null) {
            $row->taxAmount = number_format($row->taxAmount, $decimalNo);
        }
        if ($row->grossAmount !== null) {
            // $row->grossAmount = number_format($row->grossAmount, $decimalNo);
            $row->grossAmount = 1;
        }

        if ($row->convertedStandardQuantity !== null) {
            $row->convertedStandardQuantity = number_format($row->convertedStandardQuantity, $decimalNo);
        }

        if ($row->convertedStandardUnitPrice !== null) {
            $row->convertedStandardUnitPrice = number_format($row->convertedStandardUnitPrice, $decimalNo);
        }
        return $row;
    }
}
