<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaAssetLocation
 *
 * @ORM\Table(name="mla_asset_location", indexes={@ORM\Index(name="department_id_idx", columns={"department_id"}), @ORM\Index(name="user_id_idx", columns={"user_id"}), @ORM\Index(name="asset_id_idx", columns={"asset_id"})})
 * @ORM\Entity
 */
class MlaAssetLocation
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
     * @var \Application\Entity\MlaDepartments
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaDepartments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private $department;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Application\Entity\MlaAsset
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaAsset")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     * })
     */
    private $asset;



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
     * Set department
     *
     * @param \Application\Entity\MlaDepartments $department
     *
     * @return MlaAssetLocation
     */
    public function setDepartment(\Application\Entity\MlaDepartments $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Application\Entity\MlaDepartments
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set user
     *
     * @param \Application\Entity\MlaUsers $user
     *
     * @return MlaAssetLocation
     */
    public function setUser(\Application\Entity\MlaUsers $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set asset
     *
     * @param \Application\Entity\MlaAsset $asset
     *
     * @return MlaAssetLocation
     */
    public function setAsset(\Application\Entity\MlaAsset $asset = null)
    {
        $this->asset = $asset;

        return $this;
    }

    /**
     * Get asset
     *
     * @return \Application\Entity\MlaAsset
     */
    public function getAsset()
    {
        return $this->asset;
    }
}
