<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfTransitionAgent
 *
 * @ORM\Table(name="nmt_wf_transition_agent", indexes={@ORM\Index(name="nmt_wf_transition_agent_FK1_idx", columns={"transition_id"}), @ORM\Index(name="nmt_wf_transition_agent_FK2_idx", columns={"agent_id"}), @ORM\Index(name="nmt_wf_transition_agent_FK3_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtWfTransitionAgent
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
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Application\Entity\NmtWfTransition
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfTransition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transition_id", referencedColumnName="id")
     * })
     */
    private $transition;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * })
     */
    private $agent;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;



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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtWfTransitionAgent
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
     * Set transition
     *
     * @param \Application\Entity\NmtWfTransition $transition
     *
     * @return NmtWfTransitionAgent
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

    /**
     * Set agent
     *
     * @param \Application\Entity\MlaUsers $agent
     *
     * @return NmtWfTransitionAgent
     */
    public function setAgent(\Application\Entity\MlaUsers $agent = null)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtWfTransitionAgent
     */
    public function setCreatedBy(\Application\Entity\MlaUsers $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
