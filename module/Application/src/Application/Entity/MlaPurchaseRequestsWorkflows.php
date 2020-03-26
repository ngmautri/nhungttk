<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaPurchaseRequestsWorkflows
 *
 * @ORM\Table(name="mla_purchase_requests_workflows", indexes={@ORM\Index(name="mla_purchase_requests_workflows_idx", columns={"purchase_request_id"})})
 * @ORM\Entity
 */
class MlaPurchaseRequestsWorkflows
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
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="updated_by", type="integer", nullable=true)
     */
    private $updatedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var \Application\Entity\MlaPurchaseRequests
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaPurchaseRequests")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="purchase_request_id", referencedColumnName="id")
     * })
     */
    private $purchaseRequest;



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
     * @return MlaPurchaseRequestsWorkflows
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
     * Set updatedBy
     *
     * @param integer $updatedBy
     *
     * @return MlaPurchaseRequestsWorkflows
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return integer
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return MlaPurchaseRequestsWorkflows
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
     * Set purchaseRequest
     *
     * @param \Application\Entity\MlaPurchaseRequests $purchaseRequest
     *
     * @return MlaPurchaseRequestsWorkflows
     */
    public function setPurchaseRequest(\Application\Entity\MlaPurchaseRequests $purchaseRequest = null)
    {
        $this->purchaseRequest = $purchaseRequest;

        return $this;
    }

    /**
     * Get purchaseRequest
     *
     * @return \Application\Entity\MlaPurchaseRequests
     */
    public function getPurchaseRequest()
    {
        return $this->purchaseRequest;
    }
}
