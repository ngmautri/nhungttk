<?php
namespace Application\Domain\Shared\Uom\Collection;

use Application\Domain\Shared\Uom\QuantityUomGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Domain\Shared\Uom\VolumnUomGroup;
use Application\Domain\Shared\Uom\LengthUomGroup;
use Application\Domain\Shared\Uom\WeightUomGroup;
use Application\Domain\Shared\Uom\TimeUomGroup;

/**
 * Implement this to provide a list of currencies.
 *
 * @author Mathias Verraes
 */
final class UomGroups extends ArrayCollection
{

    public function __construct()
    {
        $this->add(new QuantityUomGroup());
        $this->add(new VolumnUomGroup());
        $this->add(new LengthUomGroup());
        $this->add(new WeightUomGroup());
        $this->add(new TimeUomGroup());
    }
}
