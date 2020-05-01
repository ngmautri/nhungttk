<?php
namespace Procure\Application\Reporting\AP\Output;

use Procure\Application\Service\Output\Formatter\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\AccountPayable\APRowSnapshot;

/**
 * P Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApRowFormatter extends RowFormatterDecorator
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

        // then decorate
        if ($row instanceof APRowSnapshot) {
            $row->exchangeRate = ($row->getExchangeRate() !== null ? number_format($row->getExchangeRate(), 0) : 0);
        }

        return $row;
    }
}
