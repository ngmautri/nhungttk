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
}
