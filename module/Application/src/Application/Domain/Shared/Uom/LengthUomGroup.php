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
final class LengthUomGroup extends AbstractUomGroup implements \JsonSerializable
{

    public function __construct()
    {
        $this->groupName = DefaultUomGroup::LENGHT;
        $this->members = new ArrayCollection();

        $baseUom = Uom::MILILITER();
        $this->baseUom = $baseUom;

        $m = new UomPair($baseUom, $this->baseUom, 1, 'mm');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::CENTIMETER(), 100, 'cm');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::METER(), 1000, 'm');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::INCH(), 25.4, 'in');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::YARD(), 914.4, 'yd');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::FEET(), 304.8, 'ft');
        $this->members->add($m);

        return $this;
    }
}
