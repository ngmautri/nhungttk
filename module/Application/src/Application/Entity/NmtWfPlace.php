<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfPlace
 *
 * @ORM\Table(name="nmt_wf_place", indexes={@ORM\Index(name="nmt_wf_place_FK1_idx", columns={"workflow_id"}), @ORM\Index(name="nmt_wf_place_FK2_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtWfPlace
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
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="place_name", type="string", length=80, nullable=false)
     */
    private $placeName;

    /**
     * @var string
     *
     * @ORM\Column(name="place_type", type="string", length=45, nullable=true)
     */
    private $placeType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="place_description", type="string", length=200, nullable=true)
     */
    private $placeDescription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Application\Entity\NmtWfWorkflow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfWorkflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="workflow_id", referencedColumnName="id")
     * })
     */
    private $workflow;

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
     * Set token
     *
     * @param string $token
     *
     * @return NmtWfPlace
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
     * Set placeName
     *
     * @param string $placeName
     *
     * @return NmtWfPlace
     */
    public function setPlaceName($placeName)
    {
        $this->placeName = $placeName;

        return $this;
    }

    /**
     * Get placeName
     *
     * @return string
     */
    public function getPlaceName()
    {
        return $this->placeName;
    }

    /**
     * Set placeType
     *
     * @param string $placeType
     *
     * @return NmtWfPlace
     */
    public function setPlaceType($placeType)
    {
        $this->placeType = $placeType;

        return $this;
    }

    /**
     * Get placeType
     *
     * @return string
     */
    public function getPlaceType()
    {
        return $this->placeType;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtWfPlace
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
     * Set placeDescription
     *
     * @param string $placeDescription
     *
     * @return NmtWfPlace
     */
    public function setPlaceDescription($placeDescription)
    {
        $this->placeDescription = $placeDescription;

        return $this;
    }

    /**
     * Get placeDescription
     *
     * @return string
     */
    public function getPlaceDescription()
    {
        return $this->placeDescription;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtWfPlace
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
     * Set workflow
     *
     * @param \Application\Entity\NmtWfWorkflow $workflow
     *
     * @return NmtWfPlace
     */
    public function setWorkflow(\Application\Entity\NmtWfWorkflow $workflow = null)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Get workflow
     *
     * @return \Application\Entity\NmtWfWorkflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtWfPlace
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
