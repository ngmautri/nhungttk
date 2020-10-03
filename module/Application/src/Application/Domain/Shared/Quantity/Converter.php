<?php
namespace Application\Domain\Shared\Quantity;

use Application\Domain\Shared\Uom\UomPair;

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
        if ($qty == null || $uomPair == null) {
            return;
        }

        if ($qty->getUom() == $uomPair->getBaseUom()) {
            return $qty;
        }

        if ($qty->getUom() == $uomPair->getCounterUom()) {
            return new Quantity($qty->getAmount() * $uomPair->getConvertFactor(), $uomPair->getCounterUom());
        }

        throw new \RuntimeException("Can not convert quantity");
    }
}
