<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfTransition
 *
 * @ORM\Table(name="nmt_wf_transition", indexes={@ORM\Index(name="nmt_wf_transition_idx", columns={"workflow_id"}), @ORM\Index(name="nmt_wf_transition_FK1_idx", columns={"transition_created_by"})})
 * @ORM\Entity
 */
class NmtWfTransition
{
    /**
     * @var integer
     *
     * @ORM\Column(name="transition_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $transitionId;

    /**
     * @var string
     *
     * @ORM\Column(name="transition_name", type="string", length=100, nullable=false)
     */
    private $transitionName;

    /**
     * @var string
     *
     * @ORM\Column(name="transition_description", type="text", length=65535, nullable=true)
     */
    private $transitionDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="transition_trigger", type="string", length=45, nullable=true)
     */
    private $transitionTrigger;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=45, nullable=true)
     */
    private $role;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="transition_created_on", type="datetime", nullable=false)
     */
    private $transitionCreatedOn = 'CURRENT_TIMESTAMP';

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transition_created_by", referencedColumnName="id")
     * })
     */
    private $transitionCreatedBy;



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
     * Set transitionName
     *
     * @param string $transitionName
     *
     * @return NmtWfTransition
     */
    public function setTransitionName($transitionName)
    {
        $this->transitionName = $transitionName;

        return $this;
    }

    /**
     * Get transitionName
     *
     * @return string
     */
    public function getTransitionName()
    {
        return $this->transitionName;
    }

    /**
     * Set transitionDescription
     *
     * @param string $transitionDescription
     *
     * @return NmtWfTransition
     */
    public function setTransitionDescription($transitionDescription)
    {
        $this->transitionDescription = $transitionDescription;

        return $this;
    }

    /**
     * Get transitionDescription
     *
     * @return string
     */
    public function getTransitionDescription()
    {
        return $this->transitionDescription;
    }

    /**
     * Set transitionTrigger
     *
     * @param string $transitionTrigger
     *
     * @return NmtWfTransition
     */
    public function setTransitionTrigger($transitionTrigger)
    {
        $this->transitionTrigger = $transitionTrigger;

        return $this;
    }

    /**
     * Get transitionTrigger
     *
     * @return string
     */
    public function getTransitionTrigger()
    {
        return $this->transitionTrigger;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return NmtWfTransition
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set transitionCreatedOn
     *
     * @param \DateTime $transitionCreatedOn
     *
     * @return NmtWfTransition
     */
    public function setTransitionCreatedOn($transitionCreatedOn)
    {
        $this->transitionCreatedOn = $transitionCreatedOn;

        return $this;
    }

    /**
     * Get transitionCreatedOn
     *
     * @return \DateTime
     */
    public function getTransitionCreatedOn()
    {
        return $this->transitionCreatedOn;
    }

    /**
     * Set workflow
     *
     * @param \Application\Entity\NmtWfWorkflow $workflow
     *
     * @return NmtWfTransition
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
     * Set transitionCreatedBy
     *
     * @param \Application\Entity\MlaUsers $transitionCreatedBy
     *
     * @return NmtWfTransition
     */
    public function setTransitionCreatedBy(\Application\Entity\MlaUsers $transitionCreatedBy = null)
    {
        $this->transitionCreatedBy = $transitionCreatedBy;

        return $this;
    }

    /**
     * Get transitionCreatedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getTransitionCreatedBy()
    {
        return $this->transitionCreatedBy;
    }
}
