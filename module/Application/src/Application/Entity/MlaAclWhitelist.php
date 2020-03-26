<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaAclWhitelist
 *
 * @ORM\Table(name="mla_acl_whitelist", indexes={@ORM\Index(name="mla_acl_whitelist_FK1_idx", columns={"resource_id"})})
 * @ORM\Entity
 */
class MlaAclWhitelist
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Application\Entity\MlaAclResources
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Application\Entity\MlaAclResources")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
     * })
     */
    private $resource;



    /**
     * Set id
     *
     * @param integer $id
     *
     * @return MlaAclWhitelist
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set status
     *
     * @param string $status
     *
     * @return MlaAclWhitelist
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MlaAclWhitelist
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
     * Set resource
     *
     * @param \Application\Entity\MlaAclResources $resource
     *
     * @return MlaAclWhitelist
     */
    public function setResource(\Application\Entity\MlaAclResources $resource)
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
