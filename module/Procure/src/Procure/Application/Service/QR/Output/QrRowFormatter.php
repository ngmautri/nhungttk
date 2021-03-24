<?php
namespace Procure\Application\Service\QR\Output;

use Procure\Application\Service\Output\Formatter\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\QuotationRequest\QRRowSnapshot;

/**
 * QR Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QrRowFormatter extends RowFormatterDecorator
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
        if ($row instanceof QRRowSnapshot) {
            // $row->unitPrice = ($row->getUnitPrice() !== null ? number_format($row->getUnitPrice(), $decimalNo) : 0);
        }

        return $row;
    }
}
