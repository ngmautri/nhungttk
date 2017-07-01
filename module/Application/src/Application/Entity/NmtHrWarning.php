<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrWarning
 *
 * @ORM\Table(name="nmt_hr_warning", indexes={@ORM\Index(name="nmt_hr_warning_FK1_idx", columns={"employee_id"})})
 * @ORM\Entity
 */
class NmtHrWarning
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
     * @ORM\Column(name="warning_date", type="datetime", nullable=true)
     */
    private $warningDate;

    /**
     * @var string
     *
     * @ORM\Column(name="warning_reason", type="string", length=255, nullable=true)
     */
    private $warningReason;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="created_on", type="string", length=45, nullable=true)
     */
    private $createdOn;

    /**
     * @var \Application\Entity\NmtHrEmployee
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrEmployee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * })
     */
    private $employee;



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
     * Set warningDate
     *
     * @param \DateTime $warningDate
     *
     * @return NmtHrWarning
     */
    public function setWarningDate($warningDate)
    {
        $this->warningDate = $warningDate;

        return $this;
    }

    /**
     * Get warningDate
     *
     * @return \DateTime
     */
    public function getWarningDate()
    {
        return $this->warningDate;
    }

    /**
     * Set warningReason
     *
     * @param string $warningReason
     *
     * @return NmtHrWarning
     */
    public function setWarningReason($warningReason)
    {
        $this->warningReason = $warningReason;

        return $this;
    }

    /**
     * Get warningReason
     *
     * @return string
     */
    public function getWarningReason()
    {
        return $this->warningReason;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return NmtHrWarning
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
     * @param string $createdOn
     *
     * @return NmtHrWarning
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return string
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrWarning
     */
    public function setEmployee(\Application\Entity\NmtHrEmployee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \Application\Entity\NmtHrEmployee
     */
    public function getEmployee()
    {
        return $this->employee;
    }
}
