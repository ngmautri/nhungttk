<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationAclResource
 *
 * @ORM\Table(name="nmt_application_acl_resource", uniqueConstraints={@ORM\UniqueConstraint(name="nmt_application_acl_resource_resource_UNIQUE", columns={"resource"})}, indexes={@ORM\Index(name="nmt_application_acl_resource_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_acl_resource_FK2_idx", columns={"change_by"}), @ORM\Index(name="nmt_application_acl_resource_IDX1", columns={"resource"}), @ORM\Index(name="nmt_application_acl_resource_IDX2", columns={"token"})})
 * @ORM\Entity
 */
class NmtApplicationAclResource
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
     * @ORM\Column(name="module", type="string", length=100, nullable=true)
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(name="controller", type="string", length=100, nullable=true)
     */
    private $controller;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=45, nullable=true)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="resource", type="string", length=255, nullable=false)
     */
    private $resource;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=45, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="change_on", type="datetime", nullable=true)
     */
    private $changeOn;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var integer
     *
     * @ORM\Column(name="company_id", type="integer", nullable=true)
     */
    private $companyId;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="change_by", referencedColumnName="id")
     * })
     */
    private $changeBy;



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
     * Set module
     *
     * @param string $module
     *
     * @return NmtApplicationAclResource
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set controller
     *
     * @param string $controller
     *
     * @return NmtApplicationAclResource
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return NmtApplicationAclResource
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set resource
     *
     * @param string $resource
     *
     * @return NmtApplicationAclResource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return NmtApplicationAclResource
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtApplicationAclResource
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtApplicationAclResource
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
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtApplicationAclResource
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;

        return $this;
    }

    /**
     * Get currentState
     *
     * @return string
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtApplicationAclResource
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set changeOn
     *
     * @param \DateTime $changeOn
     *
     * @return NmtApplicationAclResource
     */
    public function setChangeOn($changeOn)
    {
        $this->changeOn = $changeOn;

        return $this;
    }

    /**
     * Get changeOn
     *
     * @return \DateTime
     */
    public function getChangeOn()
    {
        return $this->changeOn;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return NmtApplicationAclResource
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set companyId
     *
     * @param integer $companyId
     *
     * @return NmtApplicationAclResource
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get companyId
     *
     * @return integer
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtApplicationAclResource
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

    /**
     * Set changeBy
     *
     * @param \Application\Entity\MlaUsers $changeBy
     *
     * @return NmtApplicationAclResource
     */
    public function setChangeBy(\Application\Entity\MlaUsers $changeBy = null)
    {
        $this->changeBy = $changeBy;

        return $this;
    }

    /**
     * Get changeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getChangeBy()
    {
        return $this->changeBy;
    }
}
