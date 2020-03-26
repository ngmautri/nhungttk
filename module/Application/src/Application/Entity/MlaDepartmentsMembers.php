<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaDepartmentsMembers
 *
 * @ORM\Table(name="mla_departments_members", indexes={@ORM\Index(name="user_id_idx", columns={"user_id"}), @ORM\Index(name="department_id_idx", columns={"department_id"}), @ORM\Index(name="mla_departments_members_FK3_idx", columns={"updated_by"})})
 * @ORM\Entity
 */
class MlaDepartmentsMembers
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
     * @var \Application\Entity\MlaDepartments
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaDepartments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private $department;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * })
     */
    private $updatedBy;



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
     * Set department
     *
     * @param \Application\Entity\MlaDepartments $department
     *
     * @return MlaDepartmentsMembers
     */
    public function setDepartment(\Application\Entity\MlaDepartments $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Application\Entity\MlaDepartments
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set user
     *
     * @param \Application\Entity\MlaUsers $user
     *
     * @return MlaDepartmentsMembers
     */
    public function setUser(\Application\Entity\MlaUsers $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set updatedBy
     *
     * @param \Application\Entity\MlaUsers $updatedBy
     *
     * @return MlaDepartmentsMembers
     */
    public function setUpdatedBy(\Application\Entity\MlaUsers $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
