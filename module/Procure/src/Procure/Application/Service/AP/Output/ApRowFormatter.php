<?php
namespace Procure\Application\Service\AP\Output;

use Procure\Application\Service\Output\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\AccountPayable\APRowSnapshot;

/**
 * AP Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApRowFormatter extends RowFormatterDecorator
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

        // then decorate
        if ($row instanceof APRowSnapshot) {
            // check later.
        }

        return $row;
    }
}
