<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfArc
 *
 * @ORM\Table(name="nmt_wf_arc", indexes={@ORM\Index(name="nmt_wf_arc_FK1_idx", columns={"workflow_id"}), @ORM\Index(name="nmt_wf_arc_FK1_idx1", columns={"place_id"}), @ORM\Index(name="nmt_wf_arc_FK3_idx", columns={"transition_id"})})
 * @ORM\Entity
 */
class NmtWfArc
{
    /**
     * @var integer
     *
     * @ORM\Column(name="arc_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $arcId;

    /**
     * @var string
     *
     * @ORM\Column(name="direction", type="string", length=45, nullable=true)
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
     * @var \Application\Entity\NmtWfWorkflow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfWorkflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="workflow_id", referencedColumnName="workflow_id")
     * })
     */
    private $workflow;

    /**
     * @var \Application\Entity\NmtWfPlace
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfPlace")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_id", referencedColumnName="place_id")
     * })
     */
    private $place;

    /**
     * @var \Application\Entity\NmtWfTransition
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfTransition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transition_id", referencedColumnName="transition_id")
     * })
     */
    private $transition;



    /**
     * Get arcId
     *
     * @return integer
     */
    public function getArcId()
    {
        return $this->arcId;
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

    /**
     * Set transition
     *
     * @param \Application\Entity\NmtWfTransition $transition
     *
     * @return NmtWfArc
     */
    public function setTransition(\Application\Entity\NmtWfTransition $transition = null)
    {
        $this->transition = $transition;

        return $this;
    }

    /**
     * Get transition
     *
     * @return \Application\Entity\NmtWfTransition
     */
    public function getTransition()
    {
        return $this->transition;
    }
}
