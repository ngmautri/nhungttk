<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaAclUserPermission
 *
 * @ORM\Table(name="mla_acl_user_permission", indexes={@ORM\Index(name="mla_acl_user_permission_idx", columns={"user_id"}), @ORM\Index(name="mla_acl_user_permission_FK1_idx", columns={"permission_id"})})
 * @ORM\Entity
 */
class MlaAclUserPermission
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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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
     * Set user
     *
     * @param \Application\Entity\MlaUsers $user
     *
     * @return MlaAclUserPermission
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
     * Set permission
     *
     * @param \Application\Entity\MlaAclPermissions $permission
     *
     * @return MlaAclUserPermission
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
