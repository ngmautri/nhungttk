<?php
namespace Application\Domain\Shared\Uom\Collection;

use Application\Domain\Shared\Uom\QuantityUomGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Domain\Shared\Uom\VolumnUomGroup;

/**
 * Implement this to provide a list of currencies.
 *
 * @author Mathias Verraes
 */
Final Class UomGroups extends ArrayCollection
{


    public function __construct()
    {
        $this->add(new QuantityUomGroup());
        $this->add(new VolumnUomGroup());

    }

}
