<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\Uom\Contracts\AbstractUomGroup;
use Application\Domain\Shared\Uom\Contracts\DefaultUomGroup;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class WeightUomGroup extends AbstractUomGroup implements \JsonSerializable
{

    public function __construct()
    {
        $this->groupName = DefaultUomGroup::WEIGHT;
        $this->members = new ArrayCollection();

        $baseUom = Uom::GRAM();
        $this->baseUom = $baseUom;

        $m = new UomPair($baseUom, $this->baseUom, 1, 'g');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::KILOGRAM(), 1000, 'kg');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::POUND(), 453.59, 'lbs');
        $this->members->add($m);


        return $this;
    }

}
