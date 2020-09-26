<?php
namespace Application\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 *         ORM@MappedSuperclass
 */
class BaseNmtInventoryWarehouse
{

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLocationList()
    {
        return $this->locationList;
    }

    // ================================
    public function __construct()
    {
        $this->locationList = new ArrayCollection();
    }

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryWarehouseLocation", mappedBy="warehouse")
     */
    private $locationList;
}
