<?php
namespace Procure\Domain\Converter;

use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\UomPair;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class QuantityConverter
{

    public function convertToBaseUom(Quantity $docQty, UomPair $uomPair)
    {
        Assert::notNull($docQty, 0);
        Assert::notNull($uomPair, 0);

        if ($docQty->getUom() == $uomPair->getBaseUom()) {

            if ($qty->getAmount() % $uomPair->getConvertFactor() == 0) {
                return new Quantity($qty->getAmount() / $uomPair->getConvertFactor(), $uomPair->getCounterUom());
            } else {
                return $qty;
            }
        }

        if ($qty->getUom() == $uomPair->getCounterUom()) {
            return new Quantity($qty->getAmount() * $uomPair->getConvertFactor(), $uomPair->getBaseUom());
        }

        throw new \RuntimeException("Can not convert quantity");
    }
}
