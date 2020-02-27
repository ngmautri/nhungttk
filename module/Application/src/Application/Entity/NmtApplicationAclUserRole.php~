<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationAclUserRole
 *
 * @ORM\Table(name="nmt_application_acl_user_role", indexes={@ORM\Index(name="nmt_application_acl_user_role_FK1_idx", columns={"user_id"}), @ORM\Index(name="nmt_application_acl_user_role_FK3_idx", columns={"updated_by"}), @ORM\Index(name="nmt_application_acl_user_role_FK2_idx", columns={"role_id"})})
 * @ORM\Entity
 */
class NmtApplicationAclUserRole
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
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

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
     * @var \Application\Entity\NmtApplicationAclRole
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationAclRole")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * })
     */
    private $role;

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
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return NmtApplicationAclUserRole
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
     * Set user
     *
     * @param \Application\Entity\MlaUsers $user
     *
     * @return NmtApplicationAclUserRole
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
     * Set role
     *
     * @param \Application\Entity\NmtApplicationAclRole $role
     *
     * @return NmtApplicationAclUserRole
     */
    public function setRole(\Application\Entity\NmtApplicationAclRole $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Application\Entity\NmtApplicationAclRole
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set updatedBy
     *
     * @param \Application\Entity\MlaUsers $updatedBy
     *
     * @return NmtApplicationAclUserRole
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
