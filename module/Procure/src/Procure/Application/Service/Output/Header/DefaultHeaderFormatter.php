<?php
namespace Procure\Application\Service\Output\Header;

use Procure\Domain\DocSnapshot;

/**
 * Default Header Formatter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultHeaderFormatter extends AbstractHeaderFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Header\AbstractHeaderFormatter::format()
     */
    public function format(DocSnapshot $header)
    {
        if (! $header instanceof DocSnapshot) {
            return null;
        }

        $decimalNo = 0;
        $curency = array(
            "USD",
            "THB",
            "EUR",
            "DKK"
        );

        if (in_array($header->getDocCurrencyISO(), $curency)) {
            $decimalNo = 2;
        }

        $header->netAmount = ($header->getBilledAmount() !== null ? number_format($header->getBilledAmount(), $decimalNo) : 0);
        $header->taxAmount = ($header->getTaxAmount() !== null ? number_format($header->getTaxAmount(), $decimalNo) : 0);
        $header->grossAmount = ($header->getGrossAmount() !== null ? number_format($header->getGrossAmount(), $decimalNo) : 0);
        return $header;
    }
}
