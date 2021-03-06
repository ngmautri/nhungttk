<?php
namespace Application\Domain\Shared\Uom;

use Doctrine\Common\Collections\ArrayCollection;
use Application\Domain\Shared\Uom\Contracts\AbstractUomGroup;
use Application\Domain\Shared\Uom\Contracts\DefaultUomGroup;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class VolumnUomGroup extends AbstractUomGroup implements \JsonSerializable
{

    public function __construct()
    {
        $this->groupName = DefaultUomGroup::VOLUMN;
        $this->members = new ArrayCollection();

        $baseUom = Uom::CUBICMETER();
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
