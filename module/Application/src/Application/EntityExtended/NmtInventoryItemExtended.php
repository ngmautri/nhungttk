<?php
namespace Application\EntityExtended;

use Doctrine\ORM\Mapping as ORM;
use Application\Entity\NmtInventoryItem;

/**
 *
 * @ORM\Entity
 */
class NmtInventoryItemExtended extends NmtInventoryItem
{

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryItemSerial", mappedBy="NmtInventoryItem")
     */
    private $serials;

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSerials()
    {
        return $this->serials;
    }

    public function __construct()
    {
        $this->serials = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
