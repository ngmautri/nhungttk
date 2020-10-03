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
final class TimeUomGroup extends AbstractUomGroup implements \JsonSerializable
{

    public function __construct()
    {
        $this->groupName = DefaultUomGroup::TIME;
        $this->members = new ArrayCollection();

        $baseUom = Uom::SECOND();
        $this->baseUom = $baseUom;

        $m = new UomPair($baseUom, $this->baseUom, 1, 'second');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::MINUTE(), 60, 'minute');
        $this->members->add($m);

        $m = new UomPair($baseUom, Uom::HOUR(), 3600, 'hour');
        $this->members->add($m);

        return $this;
    }
}
