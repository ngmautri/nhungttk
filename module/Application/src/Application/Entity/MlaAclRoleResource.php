<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaAclRoleResource
 *
 * @ORM\Table(name="mla_acl_role_resource", indexes={@ORM\Index(name="mla_acl_role_permission_FK1_idx", columns={"role_id"}), @ORM\Index(name="mla_acl_role_resource_FK2_idx", columns={"resource_id"})})
 * @ORM\Entity
 */
class MlaAclRoleResource
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
     * @var \Application\Entity\MlaAclRoles
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaAclRoles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * })
     */
    private $role;

    /**
     * @var \Application\Entity\MlaAclResources
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaAclResources")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
     * })
     */
    private $resource;



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
     * Set role
     *
     * @param \Application\Entity\MlaAclRoles $role
     *
     * @return MlaAclRoleResource
     */
    public function setRole(\Application\Entity\MlaAclRoles $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Application\Entity\MlaAclRoles
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set resource
     *
     * @param \Application\Entity\MlaAclResources $resource
     *
     * @return MlaAclRoleResource
     */
    public function setResource(\Application\Entity\MlaAclResources $resource = null)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return \Application\Entity\MlaAclResources
     */
    public function getResource()
    {
        return $this->resource;
    }
}
