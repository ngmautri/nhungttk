<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemAttachment
 *
 * @ORM\Table(name="nmt_inventory_item_attachment", indexes={@ORM\Index(name="nmt_inventory_item_attachment_FK1_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_item_attachment_FK2_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_attachment_FK3_idx", columns={"vendor_id"}), @ORM\Index(name="nmt_inventory_item_attachment_FK4_idx", columns={"item_purchasing_id"}), @ORM\Index(name="nmt_inventory_item_attachment_FK5_idx", columns={"last_change_by"})})
 * @ORM\Entity
 */
class NmtInventoryItemAttachment
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
     * @ORM\Column(name="document_type", type="string", length=50, nullable=false)
     */
    private $documentType;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="filetype", type="string", length=45, nullable=true)
     */
    private $filetype;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=100, nullable=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="filename_original", type="string", length=100, nullable=true)
     */
    private $filenameOriginal;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=45, nullable=true)
     */
    private $size;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visibility", type="boolean", nullable=true)
     */
    private $visibility;

    /**
     * @var string
     *
     * @ORM\Column(name="folder", type="string", length=255, nullable=true)
     */
    private $folder;

    /**
     * @var string
     *
     * @ORM\Column(name="folder_relative", type="string", length=100, nullable=true)
     */
    private $folderRelative;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=100, nullable=true)
     */
    private $checksum;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_from", type="datetime", nullable=true)
     */
    private $validFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_to", type="datetime", nullable=true)
     */
    private $validTo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="marked_for_deletion", type="boolean", nullable=true)
     */
    private $markedForDeletion;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

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
     * @var \Application\Entity\NmtInventoryItemPurchasing
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItemPurchasing")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_purchasing_id", referencedColumnName="id")
     * })
     */
    private $itemPurchasing;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;



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
     * Set documentType
     *
     * @param string $documentType
     *
     * @return NmtInventoryItemAttachment
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;

        return $this;
    }

    /**
     * Get documentType
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return NmtInventoryItemAttachment
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set filetype
     *
     * @param string $filetype
     *
     * @return NmtInventoryItemAttachment
     */
    public function setFiletype($filetype)
    {
        $this->filetype = $filetype;

        return $this;
    }

    /**
     * Get filetype
     *
     * @return string
     */
    public function getFiletype()
    {
        return $this->filetype;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return NmtInventoryItemAttachment
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set filenameOriginal
     *
     * @param string $filenameOriginal
     *
     * @return NmtInventoryItemAttachment
     */
    public function setFilenameOriginal($filenameOriginal)
    {
        $this->filenameOriginal = $filenameOriginal;

        return $this;
    }

    /**
     * Get filenameOriginal
     *
     * @return string
     */
    public function getFilenameOriginal()
    {
        return $this->filenameOriginal;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return NmtInventoryItemAttachment
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set visibility
     *
     * @param boolean $visibility
     *
     * @return NmtInventoryItemAttachment
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return boolean
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set folder
     *
     * @param string $folder
     *
     * @return NmtInventoryItemAttachment
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set folderRelative
     *
     * @param string $folderRelative
     *
     * @return NmtInventoryItemAttachment
     */
    public function setFolderRelative($folderRelative)
    {
        $this->folderRelative = $folderRelative;

        return $this;
    }

    /**
     * Get folderRelative
     *
     * @return string
     */
    public function getFolderRelative()
    {
        return $this->folderRelative;
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     *
     * @return NmtInventoryItemAttachment
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryItemAttachment
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
     * @return NmtInventoryItemAttachment
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
     * Set validFrom
     *
     * @param \DateTime $validFrom
     *
     * @return NmtInventoryItemAttachment
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * Get validFrom
     *
     * @return \DateTime
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * Set validTo
     *
     * @param \DateTime $validTo
     *
     * @return NmtInventoryItemAttachment
     */
    public function setValidTo($validTo)
    {
        $this->validTo = $validTo;

        return $this;
    }

    /**
     * Get validTo
     *
     * @return \DateTime
     */
    public function getValidTo()
    {
        return $this->validTo;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryItemAttachment
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtInventoryItemAttachment
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;

        return $this;
    }

    /**
     * Get lastChangeOn
     *
     * @return \DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * Set markedForDeletion
     *
     * @param boolean $markedForDeletion
     *
     * @return NmtInventoryItemAttachment
     */
    public function setMarkedForDeletion($markedForDeletion)
    {
        $this->markedForDeletion = $markedForDeletion;

        return $this;
    }

    /**
     * Get markedForDeletion
     *
     * @return boolean
     */
    public function getMarkedForDeletion()
    {
        return $this->markedForDeletion;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryItemAttachment
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryItemAttachment
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
     * Set vendor
     *
     * @param \Application\Entity\NmtBpVendor $vendor
     *
     * @return NmtInventoryItemAttachment
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
     * Set itemPurchasing
     *
     * @param \Application\Entity\NmtInventoryItemPurchasing $itemPurchasing
     *
     * @return NmtInventoryItemAttachment
     */
    public function setItemPurchasing(\Application\Entity\NmtInventoryItemPurchasing $itemPurchasing = null)
    {
        $this->itemPurchasing = $itemPurchasing;

        return $this;
    }

    /**
     * Get itemPurchasing
     *
     * @return \Application\Entity\NmtInventoryItemPurchasing
     */
    public function getItemPurchasing()
    {
        return $this->itemPurchasing;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtInventoryItemAttachment
     */
    public function setLastChangeBy(\Application\Entity\MlaUsers $lastChangeBy = null)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }
}
