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
final class QuantityUomGroup extends AbstractUomGroup implements \JsonSerializable
{

    public function __construct()
    {
        $this->members = new ArrayCollection();

        $this->groupName = DefaultUomGroup::QUANTITY;

        $baseUom = Uom::EACH();
        $this->baseUom = $baseUom;

        $m = new UomPair($baseUom, $this->baseUom, 1, 'Each');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::PIECES(), 1, 'pcs');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::PAIR(), 2, 'pair');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::DOZEN(), 12, 'dozen');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::BOX(), 25, 'Box of 25');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::BOX(), 100, 'Box of 100');
        $this->members->add($m);

        return $this;
    }
}
