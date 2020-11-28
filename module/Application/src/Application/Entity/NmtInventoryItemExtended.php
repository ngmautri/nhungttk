<?php
namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItem
 *
 * @ORM\Table(name="nmt_inventory_item", indexes={@ORM\Index(name="nmt_inventory_item_IDX1", columns={"is_active"}), @ORM\Index(name="nmt_inventory_item_IDX2", columns={"is_fixed_asset"}), @ORM\Index(name="nmt_inventory_item_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_item_FK4_idx", columns={"company_id"}), @ORM\Index(name="nmt_inventory_item_FK5_idx", columns={"last_pr_row"}), @ORM\Index(name="nmt_inventory_item_FK6_idx", columns={"last_po_row"}), @ORM\Index(name="nmt_inventory_item_FK7_idx", columns={"last_ap_invoice_row"}), @ORM\Index(name="nmt_inventory_item_FK8_idx", columns={"last_trx_row"}), @ORM\Index(name="nmt_inventory_item_FK9_idx", columns={"last_purchasing"}), @ORM\Index(name="nmt_inventory_item_FK10_idx", columns={"item_group_id"}), @ORM\Index(name="nmt_inventory_item_FK3_idx", columns={"standard_uom_id"}), @ORM\Index(name="nmt_inventory_item_FK11_idx", columns={"stock_uom_id"}), @ORM\Index(name="nmt_inventory_item_FK12_idx", columns={"cogs_account_id"}), @ORM\Index(name="nmt_inventory_item_FK13_idx", columns={"purchase_uom_id"}), @ORM\Index(name="nmt_inventory_item_FK14_idx", columns={"sales_uom_id"}), @ORM\Index(name="nmt_inventory_item_FK15_idx", columns={"inventory_account_id"}), @ORM\Index(name="nmt_inventory_item_FK16_idx", columns={"expense_account_id"}), @ORM\Index(name="nmt_inventory_item_FK17_idx", columns={"revenue_account_id"}), @ORM\Index(name="nmt_inventory_item_FK18_idx", columns={"default_warehouse_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\Repository\NmtInventoryItemRepository")
 */
class NmtInventoryItemExtended
{

    /**
     *
     * @var NmtInventoryItem
     */

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryAssociationItem", mappedBy="mainItem")
     */
    private $associationList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryAssociationItem", mappedBy="relatedItem")
     */
    private $backwardAssociationList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtProcureQoRow", mappedBy="item")
     */
    private $qoList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtProcureGrRow", mappedBy="item")
     */
    private $procureGrList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryItemSerial", mappedBy="item")
     */
    private $serialNoList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryItemBatch", mappedBy="item")
     */
    private $batchNoList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryItemPicture", mappedBy="item")
     */
    private $pictureList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtApplicationAttachment", mappedBy="item")
     */
    private $attachmentList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtProcurePrRow", mappedBy="item")
     */
    private $prList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtProcurePoRow", mappedBy="item")
     */
    private $poList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\FinVendorInvoiceRow", mappedBy="item")
     */
    private $apList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryFifoLayer", mappedBy="item")
     */
    private $fifoLayerList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryFifoLayerConsume", mappedBy="item")
     */
    private $fifoLayerConsumeList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryTrx", mappedBy="item")
     */
    private $stockGrList;

    // ================================
    public function __construct()
    {
        $this->qoList = new ArrayCollection();
        $this->procureGrList = new ArrayCollection();
        $this->serialNoList = new ArrayCollection();
        $this->batchNoList = new ArrayCollection();
        $this->pictureList = new ArrayCollection();
        $this->attachmentList = new ArrayCollection();
        $this->prList = new ArrayCollection();
        $this->poList = new ArrayCollection();
        $this->apList = new ArrayCollection();
        $this->fifoLayerList = new ArrayCollection();
        $this->fifoLayerList = new ArrayCollection();
        $this->fifoLayerConsumeList = new ArrayCollection();
        $this->stockGrList = new ArrayCollection();
        $this->associationList = new ArrayCollection();
        $this->backwardAssociationList = new ArrayCollection();
    }

    /**
     * Add associationList
     *
     * @param \Application\Entity\NmtInventoryAssociationItem $associationList
     *
     * @return NmtInventoryItemExtended
     */
    public function addAssociationList(\Application\Entity\NmtInventoryAssociationItem $associationList)
    {
        $this->associationList[] = $associationList;

        return $this;
    }

    /**
     * Remove associationList
     *
     * @param \Application\Entity\NmtInventoryAssociationItem $associationList
     */
    public function removeAssociationList(\Application\Entity\NmtInventoryAssociationItem $associationList)
    {
        $this->associationList->removeElement($associationList);
    }

    /**
     * Get associationList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssociationList()
    {
        return $this->associationList;
    }

    /**
     * Add backwardAssociationList
     *
     * @param \Application\Entity\NmtInventoryAssociationItem $backwardAssociationList
     *
     * @return NmtInventoryItemExtended
     */
    public function addBackwardAssociationList(\Application\Entity\NmtInventoryAssociationItem $backwardAssociationList)
    {
        $this->backwardAssociationList[] = $backwardAssociationList;

        return $this;
    }

    /**
     * Remove backwardAssociationList
     *
     * @param \Application\Entity\NmtInventoryAssociationItem $backwardAssociationList
     */
    public function removeBackwardAssociationList(\Application\Entity\NmtInventoryAssociationItem $backwardAssociationList)
    {
        $this->backwardAssociationList->removeElement($backwardAssociationList);
    }

    /**
     * Get backwardAssociationList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBackwardAssociationList()
    {
        return $this->backwardAssociationList;
    }

    /**
     * Add qoList
     *
     * @param \Application\Entity\NmtProcureQoRow $qoList
     *
     * @return NmtInventoryItemExtended
     */
    public function addQoList(\Application\Entity\NmtProcureQoRow $qoList)
    {
        $this->qoList[] = $qoList;

        return $this;
    }

    /**
     * Remove qoList
     *
     * @param \Application\Entity\NmtProcureQoRow $qoList
     */
    public function removeQoList(\Application\Entity\NmtProcureQoRow $qoList)
    {
        $this->qoList->removeElement($qoList);
    }

    /**
     * Get qoList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQoList()
    {
        return $this->qoList;
    }

    /**
     * Add procureGrList
     *
     * @param \Application\Entity\NmtProcureGrRow $procureGrList
     *
     * @return NmtInventoryItemExtended
     */
    public function addProcureGrList(\Application\Entity\NmtProcureGrRow $procureGrList)
    {
        $this->procureGrList[] = $procureGrList;

        return $this;
    }

    /**
     * Remove procureGrList
     *
     * @param \Application\Entity\NmtProcureGrRow $procureGrList
     */
    public function removeProcureGrList(\Application\Entity\NmtProcureGrRow $procureGrList)
    {
        $this->procureGrList->removeElement($procureGrList);
    }

    /**
     * Get procureGrList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProcureGrList()
    {
        return $this->procureGrList;
    }

    /**
     * Add serialNoList
     *
     * @param \Application\Entity\NmtInventoryItemSerial $serialNoList
     *
     * @return NmtInventoryItemExtended
     */
    public function addSerialNoList(\Application\Entity\NmtInventoryItemSerial $serialNoList)
    {
        $this->serialNoList[] = $serialNoList;

        return $this;
    }

    /**
     * Remove serialNoList
     *
     * @param \Application\Entity\NmtInventoryItemSerial $serialNoList
     */
    public function removeSerialNoList(\Application\Entity\NmtInventoryItemSerial $serialNoList)
    {
        $this->serialNoList->removeElement($serialNoList);
    }

    /**
     * Get serialNoList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSerialNoList()
    {
        return $this->serialNoList;
    }

    /**
     * Add batchNoList
     *
     * @param \Application\Entity\NmtInventoryItemBatch $batchNoList
     *
     * @return NmtInventoryItemExtended
     */
    public function addBatchNoList(\Application\Entity\NmtInventoryItemBatch $batchNoList)
    {
        $this->batchNoList[] = $batchNoList;

        return $this;
    }

    /**
     * Remove batchNoList
     *
     * @param \Application\Entity\NmtInventoryItemBatch $batchNoList
     */
    public function removeBatchNoList(\Application\Entity\NmtInventoryItemBatch $batchNoList)
    {
        $this->batchNoList->removeElement($batchNoList);
    }

    /**
     * Get batchNoList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBatchNoList()
    {
        return $this->batchNoList;
    }

    /**
     * Add pictureList
     *
     * @param \Application\Entity\NmtInventoryItemPicture $pictureList
     *
     * @return NmtInventoryItemExtended
     */
    public function addPictureList(\Application\Entity\NmtInventoryItemPicture $pictureList)
    {
        $this->pictureList[] = $pictureList;

        return $this;
    }

    /**
     * Remove pictureList
     *
     * @param \Application\Entity\NmtInventoryItemPicture $pictureList
     */
    public function removePictureList(\Application\Entity\NmtInventoryItemPicture $pictureList)
    {
        $this->pictureList->removeElement($pictureList);
    }

    /**
     * Get pictureList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPictureList()
    {
        return $this->pictureList;
    }

    /**
     * Add attachmentList
     *
     * @param \Application\Entity\NmtApplicationAttachment $attachmentList
     *
     * @return NmtInventoryItemExtended
     */
    public function addAttachmentList(\Application\Entity\NmtApplicationAttachment $attachmentList)
    {
        $this->attachmentList[] = $attachmentList;

        return $this;
    }

    /**
     * Remove attachmentList
     *
     * @param \Application\Entity\NmtApplicationAttachment $attachmentList
     */
    public function removeAttachmentList(\Application\Entity\NmtApplicationAttachment $attachmentList)
    {
        $this->attachmentList->removeElement($attachmentList);
    }

    /**
     * Get attachmentList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachmentList()
    {
        return $this->attachmentList;
    }

    /**
     * Add prList
     *
     * @param \Application\Entity\NmtProcurePrRow $prList
     *
     * @return NmtInventoryItemExtended
     */
    public function addPrList(\Application\Entity\NmtProcurePrRow $prList)
    {
        $this->prList[] = $prList;

        return $this;
    }

    /**
     * Remove prList
     *
     * @param \Application\Entity\NmtProcurePrRow $prList
     */
    public function removePrList(\Application\Entity\NmtProcurePrRow $prList)
    {
        $this->prList->removeElement($prList);
    }

    /**
     * Get prList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrList()
    {
        return $this->prList;
    }

    /**
     * Add poList
     *
     * @param \Application\Entity\NmtProcurePoRow $poList
     *
     * @return NmtInventoryItemExtended
     */
    public function addPoList(\Application\Entity\NmtProcurePoRow $poList)
    {
        $this->poList[] = $poList;

        return $this;
    }

    /**
     * Remove poList
     *
     * @param \Application\Entity\NmtProcurePoRow $poList
     */
    public function removePoList(\Application\Entity\NmtProcurePoRow $poList)
    {
        $this->poList->removeElement($poList);
    }

    /**
     * Get poList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPoList()
    {
        return $this->poList;
    }

    /**
     * Add apList
     *
     * @param \Application\Entity\FinVendorInvoiceRow $apList
     *
     * @return NmtInventoryItemExtended
     */
    public function addApList(\Application\Entity\FinVendorInvoiceRow $apList)
    {
        $this->apList[] = $apList;

        return $this;
    }

    /**
     * Remove apList
     *
     * @param \Application\Entity\FinVendorInvoiceRow $apList
     */
    public function removeApList(\Application\Entity\FinVendorInvoiceRow $apList)
    {
        $this->apList->removeElement($apList);
    }

    /**
     * Get apList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApList()
    {
        return $this->apList;
    }

    /**
     * Add fifoLayerList
     *
     * @param \Application\Entity\NmtInventoryFifoLayer $fifoLayerList
     *
     * @return NmtInventoryItemExtended
     */
    public function addFifoLayerList(\Application\Entity\NmtInventoryFifoLayer $fifoLayerList)
    {
        $this->fifoLayerList[] = $fifoLayerList;

        return $this;
    }

    /**
     * Remove fifoLayerList
     *
     * @param \Application\Entity\NmtInventoryFifoLayer $fifoLayerList
     */
    public function removeFifoLayerList(\Application\Entity\NmtInventoryFifoLayer $fifoLayerList)
    {
        $this->fifoLayerList->removeElement($fifoLayerList);
    }

    /**
     * Get fifoLayerList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFifoLayerList()
    {
        return $this->fifoLayerList;
    }

    /**
     * Add fifoLayerConsumeList
     *
     * @param \Application\Entity\NmtInventoryFifoLayerConsume $fifoLayerConsumeList
     *
     * @return NmtInventoryItemExtended
     */
    public function addFifoLayerConsumeList(\Application\Entity\NmtInventoryFifoLayerConsume $fifoLayerConsumeList)
    {
        $this->fifoLayerConsumeList[] = $fifoLayerConsumeList;

        return $this;
    }

    /**
     * Remove fifoLayerConsumeList
     *
     * @param \Application\Entity\NmtInventoryFifoLayerConsume $fifoLayerConsumeList
     */
    public function removeFifoLayerConsumeList(\Application\Entity\NmtInventoryFifoLayerConsume $fifoLayerConsumeList)
    {
        $this->fifoLayerConsumeList->removeElement($fifoLayerConsumeList);
    }

    /**
     * Get fifoLayerConsumeList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFifoLayerConsumeList()
    {
        return $this->fifoLayerConsumeList;
    }

    /**
     * Add stockGrList
     *
     * @param \Application\Entity\NmtInventoryTrx $stockGrList
     *
     * @return NmtInventoryItemExtended
     */
    public function addStockGrList(\Application\Entity\NmtInventoryTrx $stockGrList)
    {
        $this->stockGrList[] = $stockGrList;

        return $this;
    }

    /**
     * Remove stockGrList
     *
     * @param \Application\Entity\NmtInventoryTrx $stockGrList
     */
    public function removeStockGrList(\Application\Entity\NmtInventoryTrx $stockGrList)
    {
        $this->stockGrList->removeElement($stockGrList);
    }

    /**
     * Get stockGrList
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStockGrList()
    {
        return $this->stockGrList;
    }
}
