<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaAclRolePermission
 *
 * @ORM\Table(name="mla_acl_role_permission", indexes={@ORM\Index(name="mla_acl_role_permission_FK1_idx", columns={"role_id"}), @ORM\Index(name="mla_acl_role_permission_FK2_idx", columns={"permission_id"})})
 * @ORM\Entity
 */
class MlaAclRolePermission
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
     * @var \Application\Entity\MlaAclPermissions
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaAclPermissions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="permission_id", referencedColumnName="id")
     * })
     */
    private $permission;



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
     * @return MlaAclRolePermission
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
     * Set permission
     *
     * @param \Application\Entity\MlaAclPermissions $permission
     *
     * @return MlaAclRolePermission
     */
    public function setPermission(\Application\Entity\MlaAclPermissions $permission = null)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return \Application\Entity\MlaAclPermissions
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
