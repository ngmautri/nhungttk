<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcureClearingRow
 *
 * @ORM\Table(name="nmt_procure_clearing_row", indexes={@ORM\Index(name="nmt_procure_clearing_row_FK02_idx", columns={"pr_row"}), @ORM\Index(name="nmt_procure_clearing_row_FK03_idx", columns={"qo_row"}), @ORM\Index(name="nmt_procure_clearing_row_FK04_idx", columns={"po_row"}), @ORM\Index(name="nmt_procure_clearing_row_FK05_idx", columns={"gr_row"}), @ORM\Index(name="nmt_procure_clearing_row_FK06_idx", columns={"ap_row"}), @ORM\Index(name="nmt_procure_clearing_row_FK07_idx", columns={"created_by"}), @ORM\Index(name="nmt_procure_clearing_row_FK01_idx", columns={"doc_id"})})
 * @ORM\Entity
 */
class NmtProcureClearingRow
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
     * @var integer
     *
     * @ORM\Column(name="rt_row", type="integer", nullable=true)
     */
    private $rtRow;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="clearing_standard_quantity", type="decimal", precision=15, scale=6, nullable=true)
     */
    private $clearingStandardQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var \Application\Entity\NmtProcureClearingDoc
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureClearingDoc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doc_id", referencedColumnName="id")
     * })
     */
    private $doc;

    /**
     * @var \Application\Entity\NmtProcurePrRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePrRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_row", referencedColumnName="id")
     * })
     */
    private $prRow;

    /**
     * @var \Application\Entity\NmtProcureQoRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureQoRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="qo_row", referencedColumnName="id")
     * })
     */
    private $qoRow;

    /**
     * @var \Application\Entity\NmtProcurePoRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePoRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_row", referencedColumnName="id")
     * })
     */
    private $poRow;

    /**
     * @var \Application\Entity\NmtProcureGrRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureGrRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gr_row", referencedColumnName="id")
     * })
     */
    private $grRow;

    /**
     * @var \Application\Entity\FinVendorInvoiceRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoiceRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ap_row", referencedColumnName="id")
     * })
     */
    private $apRow;

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
     * @return NmtProcureClearingRow
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
     * Set rtRow
     *
     * @param integer $rtRow
     *
     * @return NmtProcureClearingRow
     */
    public function setRtRow($rtRow)
    {
        $this->rtRow = $rtRow;

        return $this;
    }

    /**
     * Get rtRow
     *
     * @return integer
     */
    public function getRtRow()
    {
        return $this->rtRow;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtProcureClearingRow
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
     * Set clearingStandardQuantity
     *
     * @param string $clearingStandardQuantity
     *
     * @return NmtProcureClearingRow
     */
    public function setClearingStandardQuantity($clearingStandardQuantity)
    {
        $this->clearingStandardQuantity = $clearingStandardQuantity;

        return $this;
    }

    /**
     * Get clearingStandardQuantity
     *
     * @return string
     */
    public function getClearingStandardQuantity()
    {
        return $this->clearingStandardQuantity;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtProcureClearingRow
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
     * Set doc
     *
     * @param \Application\Entity\NmtProcureClearingDoc $doc
     *
     * @return NmtProcureClearingRow
     */
    public function setDoc(\Application\Entity\NmtProcureClearingDoc $doc = null)
    {
        $this->doc = $doc;

        return $this;
    }

    /**
     * Get doc
     *
     * @return \Application\Entity\NmtProcureClearingDoc
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * Set prRow
     *
     * @param \Application\Entity\NmtProcurePrRow $prRow
     *
     * @return NmtProcureClearingRow
     */
    public function setPrRow(\Application\Entity\NmtProcurePrRow $prRow = null)
    {
        $this->prRow = $prRow;

        return $this;
    }

    /**
     * Get prRow
     *
     * @return \Application\Entity\NmtProcurePrRow
     */
    public function getPrRow()
    {
        return $this->prRow;
    }

    /**
     * Set qoRow
     *
     * @param \Application\Entity\NmtProcureQoRow $qoRow
     *
     * @return NmtProcureClearingRow
     */
    public function setQoRow(\Application\Entity\NmtProcureQoRow $qoRow = null)
    {
        $this->qoRow = $qoRow;

        return $this;
    }

    /**
     * Get qoRow
     *
     * @return \Application\Entity\NmtProcureQoRow
     */
    public function getQoRow()
    {
        return $this->qoRow;
    }

    /**
     * Set poRow
     *
     * @param \Application\Entity\NmtProcurePoRow $poRow
     *
     * @return NmtProcureClearingRow
     */
    public function setPoRow(\Application\Entity\NmtProcurePoRow $poRow = null)
    {
        $this->poRow = $poRow;

        return $this;
    }

    /**
     * Get poRow
     *
     * @return \Application\Entity\NmtProcurePoRow
     */
    public function getPoRow()
    {
        return $this->poRow;
    }

    /**
     * Set grRow
     *
     * @param \Application\Entity\NmtProcureGrRow $grRow
     *
     * @return NmtProcureClearingRow
     */
    public function setGrRow(\Application\Entity\NmtProcureGrRow $grRow = null)
    {
        $this->grRow = $grRow;

        return $this;
    }

    /**
     * Get grRow
     *
     * @return \Application\Entity\NmtProcureGrRow
     */
    public function getGrRow()
    {
        return $this->grRow;
    }

    /**
     * Set apRow
     *
     * @param \Application\Entity\FinVendorInvoiceRow $apRow
     *
     * @return NmtProcureClearingRow
     */
    public function setApRow(\Application\Entity\FinVendorInvoiceRow $apRow = null)
    {
        $this->apRow = $apRow;

        return $this;
    }

    /**
     * Get apRow
     *
     * @return \Application\Entity\FinVendorInvoiceRow
     */
    public function getApRow()
    {
        return $this->apRow;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtProcureClearingRow
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
