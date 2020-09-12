<?php
namespace Application\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 *         ORM@MappedSuperclass
 */
class BaseNmtInventoryItem
{

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryAssociationItem", mappedBy="mainItem")
     */
    protected $associationList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryAssociationItem", mappedBy="relatedItem")
     */
    protected $backwardAssociationList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtProcureQoRow", mappedBy="item")
     */
    protected $qoList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtProcureGrRow", mappedBy="item")
     */
    protected $procureGrList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryItemSerial", mappedBy="item")
     */
    protected $serialNoList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryItemBatch", mappedBy="item")
     */
    protected $batchNoList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryItemPicture", mappedBy="item")
     */
    protected $pictureList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtApplicationAttachment", mappedBy="item")
     */
    protected $attachmentList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtProcurePrRow", mappedBy="item")
     */
    protected $prList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtProcurePoRow", mappedBy="item")
     */
    protected $poList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\FinVendorInvoiceRow", mappedBy="item")
     */
    protected $apList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryFifoLayer", mappedBy="item")
     */
    protected $fifoLayerList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryFifoLayerConsume", mappedBy="item")
     */
    protected $fifoLayerConsumeList;

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtInventoryTrx", mappedBy="item")
     */
    protected $stockGrList;

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAssociationList()
    {
        return $this->associationList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getBackwardAssociationList()
    {
        return $this->backwardAssociationList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getQoList()
    {
        return $this->qoList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProcureGrList()
    {
        return $this->procureGrList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSerialNoList()
    {
        return $this->serialNoList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getBatchNoList()
    {
        return $this->batchNoList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPictureList()
    {
        return $this->pictureList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAttachmentList()
    {
        return $this->attachmentList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPrList()
    {
        return $this->prList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPoList()
    {
        return $this->poList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getApList()
    {
        return $this->apList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFifoLayerList()
    {
        return $this->fifoLayerList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFifoLayerConsumeList()
    {
        return $this->fifoLayerConsumeList;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getStockGrList()
    {
        return $this->stockGrList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $associationList
     */
    public function setAssociationList($associationList)
    {
        $this->associationList = $associationList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $backwardAssociationList
     */
    public function setBackwardAssociationList($backwardAssociationList)
    {
        $this->backwardAssociationList = $backwardAssociationList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $qoList
     */
    public function setQoList($qoList)
    {
        $this->qoList = $qoList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $procureGrList
     */
    public function setProcureGrList($procureGrList)
    {
        $this->procureGrList = $procureGrList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $serialNoList
     */
    public function setSerialNoList($serialNoList)
    {
        $this->serialNoList = $serialNoList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $batchNoList
     */
    public function setBatchNoList($batchNoList)
    {
        $this->batchNoList = $batchNoList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pictureList
     */
    public function setPictureList($pictureList)
    {
        $this->pictureList = $pictureList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $attachmentList
     */
    public function setAttachmentList($attachmentList)
    {
        $this->attachmentList = $attachmentList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $prList
     */
    public function setPrList($prList)
    {
        $this->prList = $prList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $poList
     */
    public function setPoList($poList)
    {
        $this->poList = $poList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $apList
     */
    public function setApList($apList)
    {
        $this->apList = $apList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fifoLayerList
     */
    public function setFifoLayerList($fifoLayerList)
    {
        $this->fifoLayerList = $fifoLayerList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fifoLayerConsumeList
     */
    public function setFifoLayerConsumeList($fifoLayerConsumeList)
    {
        $this->fifoLayerConsumeList = $fifoLayerConsumeList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $stockGrList
     */
    public function setStockGrList($stockGrList)
    {
        $this->stockGrList = $stockGrList;
    }

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
