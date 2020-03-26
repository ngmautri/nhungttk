<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcureWfToken
 *
 * @ORM\Table(name="nmt_procure_wf_token")
 * @ORM\Entity
 */
class NmtProcureWfToken
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
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=true)
     */
    private $workflowId;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_name", type="string", length=45, nullable=false)
     */
    private $workflowName;

    /**
     * @var integer
     *
     * @ORM\Column(name="place_id", type="integer", nullable=true)
     */
    private $placeId;

    /**
     * @var string
     *
     * @ORM\Column(name="place_name", type="string", length=45, nullable=false)
     */
    private $placeName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enabled_date", type="datetime", nullable=true)
     */
    private $enabledDate;

    /**
     * @var string
     *
     * @ORM\Column(name="nmt_procure_wf_tokencol", type="string", length=45, nullable=true)
     */
    private $nmtProcureWfTokencol;



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
     * Set workflowId
     *
     * @param integer $workflowId
     *
     * @return NmtProcureWfToken
     */
    public function setWorkflowId($workflowId)
    {
        $this->workflowId = $workflowId;

        return $this;
    }

    /**
     * Get workflowId
     *
     * @return integer
     */
    public function getWorkflowId()
    {
        return $this->workflowId;
    }

    /**
     * Set workflowName
     *
     * @param string $workflowName
     *
     * @return NmtProcureWfToken
     */
    public function setWorkflowName($workflowName)
    {
        $this->workflowName = $workflowName;

        return $this;
    }

    /**
     * Get workflowName
     *
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Set placeId
     *
     * @param integer $placeId
     *
     * @return NmtProcureWfToken
     */
    public function setPlaceId($placeId)
    {
        $this->placeId = $placeId;

        return $this;
    }

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
     * Set placeName
     *
     * @param string $placeName
     *
     * @return NmtProcureWfToken
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
     * Set enabledDate
     *
     * @param \DateTime $enabledDate
     *
     * @return NmtProcureWfToken
     */
    public function setEnabledDate($enabledDate)
    {
        $this->enabledDate = $enabledDate;

        return $this;
    }

    /**
     * Get enabledDate
     *
     * @return \DateTime
     */
    public function getEnabledDate()
    {
        return $this->enabledDate;
    }

    /**
     * Set nmtProcureWfTokencol
     *
     * @param string $nmtProcureWfTokencol
     *
     * @return NmtProcureWfToken
     */
    public function setNmtProcureWfTokencol($nmtProcureWfTokencol)
    {
        $this->nmtProcureWfTokencol = $nmtProcureWfTokencol;

        return $this;
    }

    /**
     * Get nmtProcureWfTokencol
     *
     * @return string
     */
    public function getNmtProcureWfTokencol()
    {
        return $this->nmtProcureWfTokencol;
    }
}
