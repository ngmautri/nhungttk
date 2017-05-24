<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfPlace
 *
 * @ORM\Table(name="nmt_wf_place", indexes={@ORM\Index(name="nmt_wf_place_FK1_idx", columns={"workflow_id"}), @ORM\Index(name="nmt_wf_place_FK2_idx", columns={"place_created_by"})})
 * @ORM\Entity
 */
class NmtWfPlace
{
    /**
     * @var integer
     *
     * @ORM\Column(name="place_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $placeId;

    /**
     * @var string
     *
     * @ORM\Column(name="place_type", type="string", length=45, nullable=true)
     */
    private $placeType;

    /**
     * @var string
     *
     * @ORM\Column(name="place_name", type="string", length=80, nullable=false)
     */
    private $placeName;

    /**
     * @var string
     *
     * @ORM\Column(name="place_description", type="text", length=65535, nullable=true)
     */
    private $placeDescription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="place_created_on", type="datetime", nullable=true)
     */
    private $placeCreatedOn;

    /**
     * @var \Application\Entity\NmtWfWorkflow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfWorkflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="workflow_id", referencedColumnName="workflow_id")
     * })
     */
    private $workflow;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_created_by", referencedColumnName="id")
     * })
     */
    private $placeCreatedBy;



    /**
     * Get placeId
     *
     * @return integer
     */
    public function getPlaceId()
    {
        return $this->placeId;
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
     * Set placeCreatedOn
     *
     * @param \DateTime $placeCreatedOn
     *
     * @return NmtWfPlace
     */
    public function setPlaceCreatedOn($placeCreatedOn)
    {
        $this->placeCreatedOn = $placeCreatedOn;

        return $this;
    }

    /**
     * Get placeCreatedOn
     *
     * @return \DateTime
     */
    public function getPlaceCreatedOn()
    {
        return $this->placeCreatedOn;
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
     * Set placeCreatedBy
     *
     * @param \Application\Entity\MlaUsers $placeCreatedBy
     *
     * @return NmtWfPlace
     */
    public function setPlaceCreatedBy(\Application\Entity\MlaUsers $placeCreatedBy = null)
    {
        $this->placeCreatedBy = $placeCreatedBy;

        return $this;
    }

    /**
     * Get placeCreatedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getPlaceCreatedBy()
    {
        return $this->placeCreatedBy;
    }
}
