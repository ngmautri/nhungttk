<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaAssetCountingItems
 *
 * @ORM\Table(name="mla_asset_counting_items", indexes={@ORM\Index(name="mla_asset_counting_items_FK_idx", columns={"counting_id"}), @ORM\Index(name="mla_asset_counting_items_FK2_idx", columns={"asset_id"})})
 * @ORM\Entity
 */
class MlaAssetCountingItems
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
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=100, nullable=true)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="counted_by", type="string", length=45, nullable=true)
     */
    private $countedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="verified_by", type="string", length=45, nullable=true)
     */
    private $verifiedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="acknowledged_by", type="string", length=45, nullable=true)
     */
    private $acknowledgedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="counted_on", type="datetime", nullable=true)
     */
    private $countedOn;

    /**
     * @var \Application\Entity\MlaAssetCounting
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaAssetCounting")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="counting_id", referencedColumnName="id")
     * })
     */
    private $counting;

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
     * Set location
     *
     * @param string $location
     *
     * @return MlaAssetCountingItems
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set countedBy
     *
     * @param string $countedBy
     *
     * @return MlaAssetCountingItems
     */
    public function setCountedBy($countedBy)
    {
        $this->countedBy = $countedBy;

        return $this;
    }

    /**
     * Get countedBy
     *
     * @return string
     */
    public function getCountedBy()
    {
        return $this->countedBy;
    }

    /**
     * Set verifiedBy
     *
     * @param string $verifiedBy
     *
     * @return MlaAssetCountingItems
     */
    public function setVerifiedBy($verifiedBy)
    {
        $this->verifiedBy = $verifiedBy;

        return $this;
    }

    /**
     * Get verifiedBy
     *
     * @return string
     */
    public function getVerifiedBy()
    {
        return $this->verifiedBy;
    }

    /**
     * Set acknowledgedBy
     *
     * @param string $acknowledgedBy
     *
     * @return MlaAssetCountingItems
     */
    public function setAcknowledgedBy($acknowledgedBy)
    {
        $this->acknowledgedBy = $acknowledgedBy;

        return $this;
    }

    /**
     * Get acknowledgedBy
     *
     * @return string
     */
    public function getAcknowledgedBy()
    {
        return $this->acknowledgedBy;
    }

    /**
     * Set countedOn
     *
     * @param \DateTime $countedOn
     *
     * @return MlaAssetCountingItems
     */
    public function setCountedOn($countedOn)
    {
        $this->countedOn = $countedOn;

        return $this;
    }

    /**
     * Get countedOn
     *
     * @return \DateTime
     */
    public function getCountedOn()
    {
        return $this->countedOn;
    }

    /**
     * Set counting
     *
     * @param \Application\Entity\MlaAssetCounting $counting
     *
     * @return MlaAssetCountingItems
     */
    public function setCounting(\Application\Entity\MlaAssetCounting $counting = null)
    {
        $this->counting = $counting;

        return $this;
    }

    /**
     * Get counting
     *
     * @return \Application\Entity\MlaAssetCounting
     */
    public function getCounting()
    {
        return $this->counting;
    }

    /**
     * Set asset
     *
     * @param \Application\Entity\MlaAsset $asset
     *
     * @return MlaAssetCountingItems
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
