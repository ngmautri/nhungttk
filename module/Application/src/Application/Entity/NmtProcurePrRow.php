<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcurePrRow
 *
 * @ORM\Table(name="nmt_procure_pr_row", indexes={@ORM\Index(name="nmt_procure_pr_row_FK1_idx", columns={"pr_id"}), @ORM\Index(name="nmt_procure_pr_row_FK2_idx", columns={"item_id"}), @ORM\Index(name="nmt_procure_pr_row_FK3_idx", columns={"vendor_id"}), @ORM\Index(name="nmt_procure_pr_row_FK4_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtProcurePrRow
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
     * @ORM\Column(name="quantiy", type="integer", nullable=true)
     */
    private $quantiy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Application\Entity\NmtProcurePr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_id", referencedColumnName="id")
     * })
     */
    private $pr;

    /**
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;

    /**
     * @var \Application\Entity\NmtBpVendor
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtBpVendor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor;

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
     * Set quantiy
     *
     * @param integer $quantiy
     *
     * @return NmtProcurePrRow
     */
    public function setQuantiy($quantiy)
    {
        $this->quantiy = $quantiy;

        return $this;
    }

    /**
     * Get quantiy
     *
     * @return integer
     */
    public function getQuantiy()
    {
        return $this->quantiy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtProcurePrRow
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
     * Set pr
     *
     * @param \Application\Entity\NmtProcurePr $pr
     *
     * @return NmtProcurePrRow
     */
    public function setPr(\Application\Entity\NmtProcurePr $pr = null)
    {
        $this->pr = $pr;

        return $this;
    }

    /**
     * Get pr
     *
     * @return \Application\Entity\NmtProcurePr
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtProcurePrRow
     */
    public function setItem(\Application\Entity\NmtInventoryItem $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \Application\Entity\NmtInventoryItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set vendor
     *
     * @param \Application\Entity\NmtBpVendor $vendor
     *
     * @return NmtProcurePrRow
     */
    public function setVendor(\Application\Entity\NmtBpVendor $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \Application\Entity\NmtBpVendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtProcurePrRow
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
