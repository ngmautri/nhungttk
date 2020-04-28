<?php
namespace Procure\Application\Service\QR\Output;

use Procure\Application\Service\Output\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Procure\Domain\QuotationRequest\QRRowSnapshot;

/**
 * AP Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowFormatter extends RowFormatterDecorator
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

        $row = $this->formatter->format($row);

        $zero = \sprintf('<span style="color:white;">0</span>');
        // $zero = 0;
        // then decorate
        if ($row instanceof QRRowSnapshot) {}

        return $row;
    }
}
