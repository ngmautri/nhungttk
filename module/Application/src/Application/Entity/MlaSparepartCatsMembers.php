<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaSparepartCatsMembers
 *
 * @ORM\Table(name="mla_sparepart_cats_members", indexes={@ORM\Index(name="mla_sparepart_cat_members_FK1_idx", columns={"sparepart_id"}), @ORM\Index(name="mla_sparepart_cats_members_FK2_idx", columns={"sparepart_cat_id"})})
 * @ORM\Entity
 */
class MlaSparepartCatsMembers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Application\Entity\MlaSpareparts
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaSpareparts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sparepart_id", referencedColumnName="id")
     * })
     */
    private $sparepart;

    /**
     * @var \Application\Entity\MlaSparepartCats
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaSparepartCats")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sparepart_cat_id", referencedColumnName="id")
     * })
     */
    private $sparepartCat;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sparepart
     *
     * @param \Application\Entity\MlaSpareparts $sparepart
     *
     * @return MlaSparepartCatsMembers
     */
    public function setSparepart(\Application\Entity\MlaSpareparts $sparepart = null)
    {
        $this->sparepart = $sparepart;

        return $this;
    }

    /**
     * Get sparepart
     *
     * @return \Application\Entity\MlaSpareparts
     */
    public function getSparepart()
    {
        return $this->sparepart;
    }

    /**
     * Set sparepartCat
     *
     * @param \Application\Entity\MlaSparepartCats $sparepartCat
     *
     * @return MlaSparepartCatsMembers
     */
    public function setSparepartCat(\Application\Entity\MlaSparepartCats $sparepartCat = null)
    {
        $this->sparepartCat = $sparepartCat;

        return $this;
    }

    /**
     * Get sparepartCat
     *
     * @return \Application\Entity\MlaSparepartCats
     */
    public function getSparepartCat()
    {
        return $this->sparepartCat;
    }
}
