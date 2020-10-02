<?php
namespace Application\Domain\Shared\Uom;

use Doctrine\Common\Collections\ArrayCollection;
use Application\Domain\Shared\Uom\Contracts\AbstractUomGroup;

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

        $baseUom = Uom::EACH();
        $this->baseUom = $baseUom;

        $m = new UomPair($baseUom, $this->baseUom, 1, 'Each');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::BOX(), 25, 'Box of 25');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::BOX(), 100, 'Box of 100');
        $this->members->add($m);

        return $this;
    }

}
