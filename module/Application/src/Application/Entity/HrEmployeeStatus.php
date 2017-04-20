<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrEmployeeStatus
 *
 * @ORM\Table(name="hr_employee_status", indexes={@ORM\Index(name="hr_employee_status_FK1_idx", columns={"employee_id"}), @ORM\Index(name="hr_employee_status_FK1_idx1", columns={"update_by"}), @ORM\Index(name="hr_employee_status_FK3_idx", columns={"new_status_id"})})
 * @ORM\Entity
 */
class HrEmployeeStatus
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
     * @ORM\Column(name="old_status_ref", type="integer", nullable=true)
     */
    private $oldStatusRef;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    private $updatedOn = 'CURRENT_TIMESTAMP';

    /**
     * @var \Application\Entity\HrEmployee
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\HrEmployee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * })
     */
    private $employee;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="update_by", referencedColumnName="id")
     * })
     */
    private $updateBy;

    /**
     * @var \Application\Entity\HrMasterStatus
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\HrMasterStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="new_status_id", referencedColumnName="id")
     * })
     */
    private $newStatus;



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
     * Set oldStatusRef
     *
     * @param integer $oldStatusRef
     *
     * @return HrEmployeeStatus
     */
    public function setOldStatusRef($oldStatusRef)
    {
        $this->oldStatusRef = $oldStatusRef;

        return $this;
    }

    /**
     * Get oldStatusRef
     *
     * @return integer
     */
    public function getOldStatusRef()
    {
        return $this->oldStatusRef;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return HrEmployeeStatus
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return \DateTime
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * Set employee
     *
     * @param \Application\Entity\HrEmployee $employee
     *
     * @return HrEmployeeStatus
     */
    public function setEmployee(\Application\Entity\HrEmployee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \Application\Entity\HrEmployee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set updateBy
     *
     * @param \Application\Entity\MlaUsers $updateBy
     *
     * @return HrEmployeeStatus
     */
    public function setUpdateBy(\Application\Entity\MlaUsers $updateBy = null)
    {
        $this->updateBy = $updateBy;

        return $this;
    }

    /**
     * Get updateBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getUpdateBy()
    {
        return $this->updateBy;
    }

    /**
     * Set newStatus
     *
     * @param \Application\Entity\HrMasterStatus $newStatus
     *
     * @return HrEmployeeStatus
     */
    public function setNewStatus(\Application\Entity\HrMasterStatus $newStatus = null)
    {
        $this->newStatus = $newStatus;

        return $this;
    }

    /**
     * Get newStatus
     *
     * @return \Application\Entity\HrMasterStatus
     */
    public function getNewStatus()
    {
        return $this->newStatus;
    }
}
