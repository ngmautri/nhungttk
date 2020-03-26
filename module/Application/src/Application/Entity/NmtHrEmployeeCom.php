<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrEmployeeCom
 *
 * @ORM\Table(name="nmt_hr_employee_com", indexes={@ORM\Index(name="nmt_hr_employee_com_FK1_idx", columns={"employee_id"})})
 * @ORM\Entity
 */
class NmtHrEmployeeCom
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
     * @ORM\Column(name="com_type", type="string", nullable=true)
     */
    private $comType;

    /**
     * @var string
     *
     * @ORM\Column(name="com_value", type="string", length=45, nullable=true)
     */
    private $comValue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

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
     * Set comType
     *
     * @param string $comType
     *
     * @return NmtHrEmployeeCom
     */
    public function setComType($comType)
    {
        $this->comType = $comType;

        return $this;
    }

    /**
     * Get comType
     *
     * @return string
     */
    public function getComType()
    {
        return $this->comType;
    }

    /**
     * Set comValue
     *
     * @param string $comValue
     *
     * @return NmtHrEmployeeCom
     */
    public function setComValue($comValue)
    {
        $this->comValue = $comValue;

        return $this;
    }

    /**
     * Get comValue
     *
     * @return string
     */
    public function getComValue()
    {
        return $this->comValue;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtHrEmployeeCom
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
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrEmployeeCom
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
