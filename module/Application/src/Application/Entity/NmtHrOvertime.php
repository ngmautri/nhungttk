<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrOvertime
 *
 * @ORM\Table(name="nmt_hr_overtime", indexes={@ORM\Index(name="nmt_hr_overtime_FK1_idx", columns={"employee_id"})})
 * @ORM\Entity
 */
class NmtHrOvertime
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
     * @ORM\Column(name="ot_from", type="datetime", nullable=true)
     */
    private $otFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ot_to", type="datetime", nullable=true)
     */
    private $otTo;

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
     * Set otFrom
     *
     * @param \DateTime $otFrom
     *
     * @return NmtHrOvertime
     */
    public function setOtFrom($otFrom)
    {
        $this->otFrom = $otFrom;

        return $this;
    }

    /**
     * Get otFrom
     *
     * @return \DateTime
     */
    public function getOtFrom()
    {
        return $this->otFrom;
    }

    /**
     * Set otTo
     *
     * @param \DateTime $otTo
     *
     * @return NmtHrOvertime
     */
    public function setOtTo($otTo)
    {
        $this->otTo = $otTo;

        return $this;
    }

    /**
     * Get otTo
     *
     * @return \DateTime
     */
    public function getOtTo()
    {
        return $this->otTo;
    }

    /**
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrOvertime
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
