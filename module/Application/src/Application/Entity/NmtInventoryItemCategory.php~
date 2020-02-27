<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemCategory
 *
 * @ORM\Table(name="nmt_inventory_item_category", uniqueConstraints={@ORM\UniqueConstraint(name="node_name_UNIQUE", columns={"node_name"})}, indexes={@ORM\Index(name="nmt_inventory_item_category_FK1_idx", columns={"node_created_by"})})
 * @ORM\Entity
 */
class NmtInventoryItemCategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="node_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $nodeId;

    /**
     * @var string
     *
     * @ORM\Column(name="node_name", type="string", length=100, nullable=false)
     */
    private $nodeName;

    /**
     * @var integer
     *
     * @ORM\Column(name="node_parent_id", type="integer", nullable=true)
     */
    private $nodeParentId;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var integer
     *
     * @ORM\Column(name="path_depth", type="integer", nullable=true)
     */
    private $pathDepth;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=200, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="node_created_on", type="datetime", nullable=true)
     */
    private $nodeCreatedOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="company_id", type="integer", nullable=true)
     */
    private $companyId;

    /**
     * @var string
     *
     * @ORM\Column(name="node_type", type="string", length=45, nullable=true)
     */
    private $nodeType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_member", type="boolean", nullable=true)
     */
    private $hasMember;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="node_created_by", referencedColumnName="id")
     * })
     */
    private $nodeCreatedBy;



    /**
     * Get nodeId
     *
     * @return integer
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * Set nodeName
     *
     * @param string $nodeName
     *
     * @return NmtInventoryItemCategory
     */
    public function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;

        return $this;
    }

    /**
     * Get nodeName
     *
     * @return string
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     * Set nodeParentId
     *
     * @param integer $nodeParentId
     *
     * @return NmtInventoryItemCategory
     */
    public function setNodeParentId($nodeParentId)
    {
        $this->nodeParentId = $nodeParentId;

        return $this;
    }

    /**
     * Get nodeParentId
     *
     * @return integer
     */
    public function getNodeParentId()
    {
        return $this->nodeParentId;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return NmtInventoryItemCategory
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set pathDepth
     *
     * @param integer $pathDepth
     *
     * @return NmtInventoryItemCategory
     */
    public function setPathDepth($pathDepth)
    {
        $this->pathDepth = $pathDepth;

        return $this;
    }

    /**
     * Get pathDepth
     *
     * @return integer
     */
    public function getPathDepth()
    {
        return $this->pathDepth;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return NmtInventoryItemCategory
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryItemCategory
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set nodeCreatedOn
     *
     * @param \DateTime $nodeCreatedOn
     *
     * @return NmtInventoryItemCategory
     */
    public function setNodeCreatedOn($nodeCreatedOn)
    {
        $this->nodeCreatedOn = $nodeCreatedOn;

        return $this;
    }

    /**
     * Get nodeCreatedOn
     *
     * @return \DateTime
     */
    public function getNodeCreatedOn()
    {
        return $this->nodeCreatedOn;
    }

    /**
     * Set companyId
     *
     * @param integer $companyId
     *
     * @return NmtInventoryItemCategory
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get companyId
     *
     * @return integer
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set nodeType
     *
     * @param string $nodeType
     *
     * @return NmtInventoryItemCategory
     */
    public function setNodeType($nodeType)
    {
        $this->nodeType = $nodeType;

        return $this;
    }

    /**
     * Get nodeType
     *
     * @return string
     */
    public function getNodeType()
    {
        return $this->nodeType;
    }

    /**
     * Set hasMember
     *
     * @param boolean $hasMember
     *
     * @return NmtInventoryItemCategory
     */
    public function setHasMember($hasMember)
    {
        $this->hasMember = $hasMember;

        return $this;
    }

    /**
     * Get hasMember
     *
     * @return boolean
     */
    public function getHasMember()
    {
        return $this->hasMember;
    }

    /**
     * Set nodeCreatedBy
     *
     * @param \Application\Entity\MlaUsers $nodeCreatedBy
     *
     * @return NmtInventoryItemCategory
     */
    public function setNodeCreatedBy(\Application\Entity\MlaUsers $nodeCreatedBy = null)
    {
        $this->nodeCreatedBy = $nodeCreatedBy;

        return $this;
    }

    /**
     * Get nodeCreatedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getNodeCreatedBy()
    {
        return $this->nodeCreatedBy;
    }
}
