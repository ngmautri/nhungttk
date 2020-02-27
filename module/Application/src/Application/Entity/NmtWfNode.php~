<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfNode
 *
 * @ORM\Table(name="nmt_wf_node", indexes={@ORM\Index(name="nmt_wf_node_idx", columns={"place_id"}), @ORM\Index(name="nmt_wf_node_FK3_idx", columns={"node_created_by"}), @ORM\Index(name="nmt_wf_node_FK4_idx", columns={"workflow_id"})})
 * @ORM\Entity
 */
class NmtWfNode
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
     * @ORM\Column(name="node_name", type="string", length=100, nullable=true)
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
     * @ORM\Column(name="node_type", type="string", length=45, nullable=true)
     */
    private $nodeType;

    /**
     * @var string
     *
     * @ORM\Column(name="node_connection_type", type="string", length=45, nullable=true)
     */
    private $nodeConnectionType;

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
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
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
     * @ORM\Column(name="transition_id", type="integer", nullable=true)
     */
    private $transitionId;

    /**
     * @var \Application\Entity\NmtWfPlace
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfPlace")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     * })
     */
    private $place;

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
     * @var \Application\Entity\NmtWfWorkflow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfWorkflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="workflow_id", referencedColumnName="id")
     * })
     */
    private $workflow;



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
     * @return NmtWfNode
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
     * @return NmtWfNode
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
     * Set nodeType
     *
     * @param string $nodeType
     *
     * @return NmtWfNode
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
     * Set nodeConnectionType
     *
     * @param string $nodeConnectionType
     *
     * @return NmtWfNode
     */
    public function setNodeConnectionType($nodeConnectionType)
    {
        $this->nodeConnectionType = $nodeConnectionType;

        return $this;
    }

    /**
     * Get nodeConnectionType
     *
     * @return string
     */
    public function getNodeConnectionType()
    {
        return $this->nodeConnectionType;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return NmtWfNode
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
     * @return NmtWfNode
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
     * @return NmtWfNode
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
     * @return NmtWfNode
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
     * @return NmtWfNode
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
     * Set transitionId
     *
     * @param integer $transitionId
     *
     * @return NmtWfNode
     */
    public function setTransitionId($transitionId)
    {
        $this->transitionId = $transitionId;

        return $this;
    }

    /**
     * Get transitionId
     *
     * @return integer
     */
    public function getTransitionId()
    {
        return $this->transitionId;
    }

    /**
     * Set place
     *
     * @param \Application\Entity\NmtWfPlace $place
     *
     * @return NmtWfNode
     */
    public function setPlace(\Application\Entity\NmtWfPlace $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \Application\Entity\NmtWfPlace
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set nodeCreatedBy
     *
     * @param \Application\Entity\MlaUsers $nodeCreatedBy
     *
     * @return NmtWfNode
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

    /**
     * Set workflow
     *
     * @param \Application\Entity\NmtWfWorkflow $workflow
     *
     * @return NmtWfNode
     */
    public function setWorkflow(\Application\Entity\NmtWfWorkflow $workflow = null)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Get workflow
     *
     * @return \Application\Entity\NmtWfWorkflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }
}
