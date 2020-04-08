<?php
namespace Procure\Application\Service\Output;

use Procure\Domain\RowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowNumberFormatter extends AbstractRowFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\AbstractRowFormatter::format()
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

        if ($row->docUnitPrice !== null) {
            $row->docUnitPrice = number_format($row->docUnitPrice, $decimalNo);
        }

        if ($row->netAmount !== null) {
            $row->netAmount = number_format($row->netAmount, $decimalNo);
        }

        if ($row->taxAmount !== null) {
            $row->taxAmount = number_format($row->taxAmount, $decimalNo);
        }
        if ($row->grossAmount !== null) {
            $row->grossAmount = number_format($row->grossAmount, $decimalNo);
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
