<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrLeaveReason
 *
 * @ORM\Table(name="nmt_hr_leave_reason", indexes={@ORM\Index(name="nmt_hr_leave_reason_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_hr_leave_reason_FK2_idx", columns={"lastchange_by"})})
 * @ORM\Entity
 */
class NmtHrLeaveReason
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
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="leave_reason", type="string", length=60, nullable=false)
     */
    private $leaveReason;

    /**
     * @var string
     *
     * @ORM\Column(name="leave_reason_local", type="string", length=60, nullable=true)
     */
    private $leaveReasonLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_from", type="datetime", nullable=true)
     */
    private $validFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_to", type="datetime", nullable=true)
     */
    private $validTo;

    /**
     * @var string
     *
     * @ORM\Column(name="conditions", type="string", length=80, nullable=true)
     */
    private $conditions;

    /**
     * @var string
     *
     * @ORM\Column(name="legal_reference", type="string", length=80, nullable=true)
     */
    private $legalReference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lastchange_by", referencedColumnName="id")
     * })
     */
    private $lastchangeBy;



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
     * Set token
     *
     * @param string $token
     *
     * @return NmtHrLeaveReason
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set leaveReason
     *
     * @param string $leaveReason
     *
     * @return NmtHrLeaveReason
     */
    public function setLeaveReason($leaveReason)
    {
        $this->leaveReason = $leaveReason;

        return $this;
    }

    /**
     * Get leaveReason
     *
     * @return string
     */
    public function getLeaveReason()
    {
        return $this->leaveReason;
    }

    /**
     * Set leaveReasonLocal
     *
     * @param string $leaveReasonLocal
     *
     * @return NmtHrLeaveReason
     */
    public function setLeaveReasonLocal($leaveReasonLocal)
    {
        $this->leaveReasonLocal = $leaveReasonLocal;

        return $this;
    }

    /**
     * Get leaveReasonLocal
     *
     * @return string
     */
    public function getLeaveReasonLocal()
    {
        return $this->leaveReasonLocal;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return NmtHrLeaveReason
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtHrLeaveReason
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set validFrom
     *
     * @param \DateTime $validFrom
     *
     * @return NmtHrLeaveReason
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * Get validFrom
     *
     * @return \DateTime
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * Set validTo
     *
     * @param \DateTime $validTo
     *
     * @return NmtHrLeaveReason
     */
    public function setValidTo($validTo)
    {
        $this->validTo = $validTo;

        return $this;
    }

    /**
     * Get validTo
     *
     * @return \DateTime
     */
    public function getValidTo()
    {
        return $this->validTo;
    }

    /**
     * Set conditions
     *
     * @param string $conditions
     *
     * @return NmtHrLeaveReason
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * Get conditions
     *
     * @return string
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Set legalReference
     *
     * @param string $legalReference
     *
     * @return NmtHrLeaveReason
     */
    public function setLegalReference($legalReference)
    {
        $this->legalReference = $legalReference;

        return $this;
    }

    /**
     * Get legalReference
     *
     * @return string
     */
    public function getLegalReference()
    {
        return $this->legalReference;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtHrLeaveReason
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
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return NmtHrLeaveReason
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;

        return $this;
    }

    /**
     * Get lastchangeOn
     *
     * @return \DateTime
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtHrLeaveReason
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

    /**
     * Set lastchangeBy
     *
     * @param \Application\Entity\MlaUsers $lastchangeBy
     *
     * @return NmtHrLeaveReason
     */
    public function setLastchangeBy(\Application\Entity\MlaUsers $lastchangeBy = null)
    {
        $this->lastchangeBy = $lastchangeBy;

        return $this;
    }

    /**
     * Get lastchangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }
}
