<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrEmployeePosition
 *
 * @ORM\Table(name="nmt_hr_employee_position", uniqueConstraints={@ORM\UniqueConstraint(name="position_id_UNIQUE", columns={"position_id"})}, indexes={@ORM\Index(name="nmt_hr_employee_position_FK1_idx", columns={"employee_id"})})
 * @ORM\Entity
 */
class NmtHrEmployeePosition
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
     * @var \Application\Entity\NmtHrEmployee
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrEmployee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * })
     */
    private $employee;

    /**
     * @var \Application\Entity\NmtHrPosition
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrPosition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="position_id", referencedColumnName="id")
     * })
     */
    private $position;



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
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrEmployeePosition
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

    /**
     * Set position
     *
     * @param \Application\Entity\NmtHrPosition $position
     *
     * @return NmtHrEmployeePosition
     */
    public function setPosition(\Application\Entity\NmtHrPosition $position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return \Application\Entity\NmtHrPosition
     */
    public function getPosition()
    {
        return $this->position;
    }
}
