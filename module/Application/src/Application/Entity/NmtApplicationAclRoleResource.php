<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationAclRoleResource
 *
 * @ORM\Table(name="nmt_application_acl_role_resource", indexes={@ORM\Index(name="nmt_application_acl_role_resource_FK3_idx", columns={"updated_by"}), @ORM\Index(name="nmt_application_acl_role_resource_FK1_idx", columns={"role_id"}), @ORM\Index(name="nmt_application_acl_role_resource_FK2_idx", columns={"resource_id"})})
 * @ORM\Entity
 */
class NmtApplicationAclRoleResource
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
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    private $updatedOn = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="nmt_application_acl_role_resourcecol", type="string", length=45, nullable=true)
     */
    private $nmtApplicationAclRoleResourcecol;

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
     * @var \Application\Entity\NmtApplicationAclResource
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationAclResource")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
     * })
     */
    private $resource;

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
     * @return NmtApplicationAclRoleResource
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
     * Set nmtApplicationAclRoleResourcecol
     *
     * @param string $nmtApplicationAclRoleResourcecol
     *
     * @return NmtApplicationAclRoleResource
     */
    public function setNmtApplicationAclRoleResourcecol($nmtApplicationAclRoleResourcecol)
    {
        $this->nmtApplicationAclRoleResourcecol = $nmtApplicationAclRoleResourcecol;

        return $this;
    }

    /**
     * Get nmtApplicationAclRoleResourcecol
     *
     * @return string
     */
    public function getNmtApplicationAclRoleResourcecol()
    {
        return $this->nmtApplicationAclRoleResourcecol;
    }

    /**
     * Set role
     *
     * @param \Application\Entity\NmtApplicationAclRole $role
     *
     * @return NmtApplicationAclRoleResource
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
     * Set resource
     *
     * @param \Application\Entity\NmtApplicationAclResource $resource
     *
     * @return NmtApplicationAclRoleResource
     */
    public function setResource(\Application\Entity\NmtApplicationAclResource $resource = null)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return \Application\Entity\NmtApplicationAclResource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set updatedBy
     *
     * @param \Application\Entity\MlaUsers $updatedBy
     *
     * @return NmtApplicationAclRoleResource
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
