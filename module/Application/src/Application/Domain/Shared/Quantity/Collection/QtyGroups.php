<?php
namespace Application\Domain\Shared\Uom\Collection;

use Application\Domain\Shared\Uom\QuantityUomGroup;
use Application\Domain\Shared\Uom\VolumnUomGroup;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Implement this to provide a list of currencies.
 *
 * @author Mathias Verraes
 */
Final Class QtyGroups extends ArrayCollection
{


    public function __construct()
    {
        $this->add(new QuantityUomGroup());
        $this->add(new VolumnUomGroup());

    }

}
