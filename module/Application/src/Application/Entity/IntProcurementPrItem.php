<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IntProcurementPrItem
 *
 * @ORM\Table(name="int_procurement_pr_item")
 * @ORM\Entity
 */
class IntProcurementPrItem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="int", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $int;



    /**
     * Get int
     *
     * @return integer
     */
    public function getInt()
    {
        return $this->int;
    }
}
