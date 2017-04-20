<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaSparepartMovements
 *
 * @ORM\Table(name="mla_sparepart_movements", indexes={@ORM\Index(name="mla_spartpart_movements_FK1_idx", columns={"sparepart_id"}), @ORM\Index(name="mla_sparepart_movements_FK2_idx", columns={"wh_id"})})
 * @ORM\Entity
 */
class MlaSparepartMovements
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
     * @var \DateTime
     *
     * @ORM\Column(name="movement_date", type="datetime", nullable=false)
     */
    private $movementDate = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="asset_id", type="integer", nullable=true)
     */
    private $assetId;

    /**
     * @var string
     *
     * @ORM\Column(name="flow", type="string", length=45, nullable=true)
     */
    private $flow;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=45, nullable=true)
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(name="requester", type="string", length=50, nullable=true)
     */
    private $requester;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="asset_name", type="string", length=100, nullable=true)
     */
    private $assetName;

    /**
     * @var string
     *
     * @ORM\Column(name="movement_type", type="string", length=100, nullable=true)
     */
    private $movementType;

    /**
     * @var string
     *
     * @ORM\Column(name="asset_location", type="string", length=100, nullable=true)
     */
    private $assetLocation;

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
     * @var \Application\Entity\NmtInventoryWarehouse
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="wh_id", referencedColumnName="id")
     * })
     */
    private $wh;



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
     * Set movementDate
     *
     * @param \DateTime $movementDate
     *
     * @return MlaSparepartMovements
     */
    public function setMovementDate($movementDate)
    {
        $this->movementDate = $movementDate;

        return $this;
    }

    /**
     * Get movementDate
     *
     * @return \DateTime
     */
    public function getMovementDate()
    {
        return $this->movementDate;
    }

    /**
     * Set assetId
     *
     * @param integer $assetId
     *
     * @return MlaSparepartMovements
     */
    public function setAssetId($assetId)
    {
        $this->assetId = $assetId;

        return $this;
    }

    /**
     * Get assetId
     *
     * @return integer
     */
    public function getAssetId()
    {
        return $this->assetId;
    }

    /**
     * Set flow
     *
     * @param string $flow
     *
     * @return MlaSparepartMovements
     */
    public function setFlow($flow)
    {
        $this->flow = $flow;

        return $this;
    }

    /**
     * Get flow
     *
     * @return string
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return MlaSparepartMovements
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return MlaSparepartMovements
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set requester
     *
     * @param string $requester
     *
     * @return MlaSparepartMovements
     */
    public function setRequester($requester)
    {
        $this->requester = $requester;

        return $this;
    }

    /**
     * Get requester
     *
     * @return string
     */
    public function getRequester()
    {
        return $this->requester;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return MlaSparepartMovements
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MlaSparepartMovements
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set assetName
     *
     * @param string $assetName
     *
     * @return MlaSparepartMovements
     */
    public function setAssetName($assetName)
    {
        $this->assetName = $assetName;

        return $this;
    }

    /**
     * Get assetName
     *
     * @return string
     */
    public function getAssetName()
    {
        return $this->assetName;
    }

    /**
     * Set movementType
     *
     * @param string $movementType
     *
     * @return MlaSparepartMovements
     */
    public function setMovementType($movementType)
    {
        $this->movementType = $movementType;

        return $this;
    }

    /**
     * Get movementType
     *
     * @return string
     */
    public function getMovementType()
    {
        return $this->movementType;
    }

    /**
     * Set assetLocation
     *
     * @param string $assetLocation
     *
     * @return MlaSparepartMovements
     */
    public function setAssetLocation($assetLocation)
    {
        $this->assetLocation = $assetLocation;

        return $this;
    }

    /**
     * Get assetLocation
     *
     * @return string
     */
    public function getAssetLocation()
    {
        return $this->assetLocation;
    }

    /**
     * Set sparepart
     *
     * @param \Application\Entity\MlaSpareparts $sparepart
     *
     * @return MlaSparepartMovements
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
     * Set wh
     *
     * @param \Application\Entity\NmtInventoryWarehouse $wh
     *
     * @return MlaSparepartMovements
     */
    public function setWh(\Application\Entity\NmtInventoryWarehouse $wh = null)
    {
        $this->wh = $wh;

        return $this;
    }

    /**
     * Get wh
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getWh()
    {
        return $this->wh;
    }
}
