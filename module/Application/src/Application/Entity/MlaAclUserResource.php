<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaAclUserResource
 *
 * @ORM\Table(name="mla_acl_user_resource", indexes={@ORM\Index(name="mla_acl_user_permission_idx", columns={"user_id"}), @ORM\Index(name="mla_acl_user_resource_FK2_idx", columns={"resource_id"})})
 * @ORM\Entity
 */
class MlaAclUserResource
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
     * Set user
     *
     * @param \Application\Entity\MlaUsers $user
     *
     * @return MlaAclUserResource
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
     * Set resource
     *
     * @param \Application\Entity\MlaAclResources $resource
     *
     * @return MlaAclUserResource
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
