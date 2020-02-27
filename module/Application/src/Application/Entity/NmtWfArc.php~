<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfArc
 *
 * @ORM\Table(name="nmt_wf_arc", indexes={@ORM\Index(name="nmt_wf_arc_FK1_idx1", columns={"place_id"}), @ORM\Index(name="nmt_wf_arc_FK1_idx", columns={"workflow_id"})})
 * @ORM\Entity
 */
class NmtWfArc
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
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    private $weight;

    /**
     * @var integer
     *
     * @ORM\Column(name="transition_id", type="integer", nullable=false)
     */
    private $transitionId;

    /**
     * @var string
     *
     * @ORM\Column(name="direction", type="string", nullable=true)
     */
    private $direction;

    /**
     * @var string
     *
     * @ORM\Column(name="arc_type", type="string", length=45, nullable=true)
     */
    private $arcType;

    /**
     * @var string
     *
     * @ORM\Column(name="condition", type="string", length=45, nullable=true)
     */
    private $condition;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

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
     * @var \Application\Entity\NmtWfPlace
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfPlace")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     * })
     */
    private $place;



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
     * Set weight
     *
     * @param integer $weight
     *
     * @return NmtWfArc
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set transitionId
     *
     * @param integer $transitionId
     *
     * @return NmtWfArc
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
     * Set direction
     *
     * @param string $direction
     *
     * @return NmtWfArc
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set arcType
     *
     * @param string $arcType
     *
     * @return NmtWfArc
     */
    public function setArcType($arcType)
    {
        $this->arcType = $arcType;

        return $this;
    }

    /**
     * Get arcType
     *
     * @return string
     */
    public function getArcType()
    {
        return $this->arcType;
    }

    /**
     * Set condition
     *
     * @param string $condition
     *
     * @return NmtWfArc
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Get condition
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return NmtWfArc
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtWfArc
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
     * Set workflow
     *
     * @param \Application\Entity\NmtWfWorkflow $workflow
     *
     * @return NmtWfArc
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

    /**
     * Set place
     *
     * @param \Application\Entity\NmtWfPlace $place
     *
     * @return NmtWfArc
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
}
