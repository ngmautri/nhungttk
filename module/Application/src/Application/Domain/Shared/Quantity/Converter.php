<?php
namespace Application\Domain\Shared\Quantity;

use Application\Domain\Shared\Uom\UomPair;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class Converter
{

    /**
     *
     * @param Quantity $qty
     * @param UomPair $uomPair
     * @return void|\Application\Domain\Shared\Quantity\Quantity
     */
    public function convertToBaseUom(Quantity $qty, UomPair $uomPair)
    {
        Assert::notNull($qty, 0);
        Assert::notNull($uomPair);

        if ($qty->getUom() == $uomPair->getBaseUom()) {

            if ($qty->getAmount() % $uomPair->getConvertFactor() == 0) {
                return new Quantity($qty->getAmount() / $uomPair->getConvertFactor(), $uomPair->getCounterUomObject());
            } else {
                return $qty;
            }
        }

        if ($qty->getUom() == $uomPair->getCounterUom()) {
            return new Quantity($qty->getAmount() * $uomPair->getConvertFactor(), $uomPair->getBaseUomObject());
        }

        throw new \InvalidArgumentException("Can not convert quantity");
    }
}
