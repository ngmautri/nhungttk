<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrMasterPosition
 *
 * @ORM\Table(name="hr_master_position", indexes={@ORM\Index(name="hr_master_status_FK1_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class HrMasterPosition
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
     * @ORM\Column(name="position_name", type="string", length=100, nullable=false)
     */
    private $positionName;

    /**
     * @var string
     *
     * @ORM\Column(name="position_description", type="text", length=65535, nullable=false)
     */
    private $positionDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_center", type="string", length=45, nullable=true)
     */
    private $costCenter;

    /**
     * @var string
     *
     * @ORM\Column(name="department", type="string", length=45, nullable=true)
     */
    private $department;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

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
     * Set positionName
     *
     * @param string $positionName
     *
     * @return HrMasterPosition
     */
    public function setPositionName($positionName)
    {
        $this->positionName = $positionName;

        return $this;
    }

    /**
     * Get positionName
     *
     * @return string
     */
    public function getPositionName()
    {
        return $this->positionName;
    }

    /**
     * Set positionDescription
     *
     * @param string $positionDescription
     *
     * @return HrMasterPosition
     */
    public function setPositionDescription($positionDescription)
    {
        $this->positionDescription = $positionDescription;

        return $this;
    }

    /**
     * Get positionDescription
     *
     * @return string
     */
    public function getPositionDescription()
    {
        return $this->positionDescription;
    }

    /**
     * Set costCenter
     *
     * @param string $costCenter
     *
     * @return HrMasterPosition
     */
    public function setCostCenter($costCenter)
    {
        $this->costCenter = $costCenter;

        return $this;
    }

    /**
     * Get costCenter
     *
     * @return string
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     * Set department
     *
     * @param string $department
     *
     * @return HrMasterPosition
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return HrMasterPosition
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return HrMasterPosition
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
