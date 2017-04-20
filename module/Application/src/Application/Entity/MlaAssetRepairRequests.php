<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaAssetRepairRequests
 *
 * @ORM\Table(name="mla_asset_repair_requests", indexes={@ORM\Index(name="asset_id_idx", columns={"asset_id"})})
 * @ORM\Entity
 */
class MlaAssetRepairRequests
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
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=45, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="requested_by", type="integer", nullable=true)
     */
    private $requestedBy;

    /**
     * @var integer
     *
     * @ORM\Column(name="approved_by", type="integer", nullable=true)
     */
    private $approvedBy;

    /**
     * @var integer
     *
     * @ORM\Column(name="assigned_to", type="integer", nullable=true)
     */
    private $assignedTo;

    /**
     * @var string
     *
     * @ORM\Column(name="assigned_to_name", type="string", length=45, nullable=true)
     */
    private $assignedToName;

    /**
     * @var string
     *
     * @ORM\Column(name="result", type="string", length=100, nullable=true)
     */
    private $result;

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
     * Set name
     *
     * @param string $name
     *
     * @return MlaAssetRepairRequests
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return MlaAssetRepairRequests
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set requestedBy
     *
     * @param integer $requestedBy
     *
     * @return MlaAssetRepairRequests
     */
    public function setRequestedBy($requestedBy)
    {
        $this->requestedBy = $requestedBy;

        return $this;
    }

    /**
     * Get requestedBy
     *
     * @return integer
     */
    public function getRequestedBy()
    {
        return $this->requestedBy;
    }

    /**
     * Set approvedBy
     *
     * @param integer $approvedBy
     *
     * @return MlaAssetRepairRequests
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * Get approvedBy
     *
     * @return integer
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * Set assignedTo
     *
     * @param integer $assignedTo
     *
     * @return MlaAssetRepairRequests
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * Get assignedTo
     *
     * @return integer
     */
    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    /**
     * Set assignedToName
     *
     * @param string $assignedToName
     *
     * @return MlaAssetRepairRequests
     */
    public function setAssignedToName($assignedToName)
    {
        $this->assignedToName = $assignedToName;

        return $this;
    }

    /**
     * Get assignedToName
     *
     * @return string
     */
    public function getAssignedToName()
    {
        return $this->assignedToName;
    }

    /**
     * Set result
     *
     * @param string $result
     *
     * @return MlaAssetRepairRequests
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set asset
     *
     * @param \Application\Entity\MlaAsset $asset
     *
     * @return MlaAssetRepairRequests
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
