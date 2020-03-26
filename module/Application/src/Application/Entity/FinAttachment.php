<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinAttachment
 *
 * @ORM\Table(name="fin_attachment", indexes={@ORM\Index(name="fin_attachment_FK1_idx", columns={"created_by"}), @ORM\Index(name="fin_attachment_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="fin_attachment_FK3_idx", columns={"project_id"}), @ORM\Index(name="fin_attachment_FK4_idx", columns={"employee_id"}), @ORM\Index(name="fin_attachment_FK5_idx", columns={"vendor_id"}), @ORM\Index(name="fin_attachment_FK6_idx", columns={"item_purchasing_id"}), @ORM\Index(name="fin_attachment_FK7_idx", columns={"pr_id"}), @ORM\Index(name="fin_attachment_FK8_idx", columns={"item_id"})})
 * @ORM\Entity
 */
class FinAttachment
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
     * @ORM\Column(name="document_subject", type="string", length=100, nullable=false)
     */
    private $documentSubject;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=150, nullable=true)
     */
    private $keywords;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_picture", type="boolean", nullable=true)
     */
    private $isPicture;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_contract", type="boolean", nullable=true)
     */
    private $isContract;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="signing_date", type="datetime", nullable=true)
     */
    private $signingDate;

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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=150, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="filetype", type="string", length=150, nullable=true)
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
     * @ORM\Column(name="filename_original", type="string", length=150, nullable=true)
     */
    private $filenameOriginal;

    /**
     * @var string
     *
     * @ORM\Column(name="file_password", type="string", length=45, nullable=true)
     */
    private $filePassword;

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
     * @ORM\Column(name="attachment_folder", type="string", length=200, nullable=true)
     */
    private $attachmentFolder;

    /**
     * @var string
     *
     * @ORM\Column(name="folder_relative", type="string", length=150, nullable=true)
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
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="marked_for_deletion", type="boolean", nullable=true)
     */
    private $markedForDeletion;

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
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="change_for", type="integer", nullable=true)
     */
    private $changeFor;

    /**
     * @var integer
     *
     * @ORM\Column(name="pr_row_id", type="integer", nullable=true)
     */
    private $prRowId;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;

    /**
     * @var \Application\Entity\NmtPmProject
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtPmProject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;

    /**
     * @var \Application\Entity\NmtHrEmployee
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrEmployee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * })
     */
    private $employee;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set documentSubject
     *
     * @param string $documentSubject
     *
     * @return FinAttachment
     */
    public function setDocumentSubject($documentSubject)
    {
        $this->documentSubject = $documentSubject;

        return $this;
    }

    /**
     * Get documentSubject
     *
     * @return string
     */
    public function getDocumentSubject()
    {
        return $this->documentSubject;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return FinAttachment
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set isPicture
     *
     * @param boolean $isPicture
     *
     * @return FinAttachment
     */
    public function setIsPicture($isPicture)
    {
        $this->isPicture = $isPicture;

        return $this;
    }

    /**
     * Get isPicture
     *
     * @return boolean
     */
    public function getIsPicture()
    {
        return $this->isPicture;
    }

    /**
     * Set isContract
     *
     * @param boolean $isContract
     *
     * @return FinAttachment
     */
    public function setIsContract($isContract)
    {
        $this->isContract = $isContract;

        return $this;
    }

    /**
     * Get isContract
     *
     * @return boolean
     */
    public function getIsContract()
    {
        return $this->isContract;
    }

    /**
     * Set signingDate
     *
     * @param \DateTime $signingDate
     *
     * @return FinAttachment
     */
    public function setSigningDate($signingDate)
    {
        $this->signingDate = $signingDate;

        return $this;
    }

    /**
     * Get signingDate
     *
     * @return \DateTime
     */
    public function getSigningDate()
    {
        return $this->signingDate;
    }

    /**
     * Set validFrom
     *
     * @param \DateTime $validFrom
     *
     * @return FinAttachment
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
     * @return FinAttachment
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
     * Set url
     *
     * @param string $url
     *
     * @return FinAttachment
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
     * @return FinAttachment
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
     * @return FinAttachment
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
     * @return FinAttachment
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
     * Set filePassword
     *
     * @param string $filePassword
     *
     * @return FinAttachment
     */
    public function setFilePassword($filePassword)
    {
        $this->filePassword = $filePassword;

        return $this;
    }

    /**
     * Get filePassword
     *
     * @return string
     */
    public function getFilePassword()
    {
        return $this->filePassword;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return FinAttachment
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
     * @return FinAttachment
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
     * @return FinAttachment
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
     * Set attachmentFolder
     *
     * @param string $attachmentFolder
     *
     * @return FinAttachment
     */
    public function setAttachmentFolder($attachmentFolder)
    {
        $this->attachmentFolder = $attachmentFolder;

        return $this;
    }

    /**
     * Get attachmentFolder
     *
     * @return string
     */
    public function getAttachmentFolder()
    {
        return $this->attachmentFolder;
    }

    /**
     * Set folderRelative
     *
     * @param string $folderRelative
     *
     * @return FinAttachment
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
     * @return FinAttachment
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
     * Set token
     *
     * @param string $token
     *
     * @return FinAttachment
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return FinAttachment
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
     * Set markedForDeletion
     *
     * @param boolean $markedForDeletion
     *
     * @return FinAttachment
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return FinAttachment
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
     * @return FinAttachment
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return FinAttachment
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
     * Set changeFor
     *
     * @param integer $changeFor
     *
     * @return FinAttachment
     */
    public function setChangeFor($changeFor)
    {
        $this->changeFor = $changeFor;

        return $this;
    }

    /**
     * Get changeFor
     *
     * @return integer
     */
    public function getChangeFor()
    {
        return $this->changeFor;
    }

    /**
     * Set prRowId
     *
     * @param integer $prRowId
     *
     * @return FinAttachment
     */
    public function setPrRowId($prRowId)
    {
        $this->prRowId = $prRowId;

        return $this;
    }

    /**
     * Get prRowId
     *
     * @return integer
     */
    public function getPrRowId()
    {
        return $this->prRowId;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return FinAttachment
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
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return FinAttachment
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

    /**
     * Set project
     *
     * @param \Application\Entity\NmtPmProject $project
     *
     * @return FinAttachment
     */
    public function setProject(\Application\Entity\NmtPmProject $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Application\Entity\NmtPmProject
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return FinAttachment
     */
    public function setEmployee(\Application\Entity\NmtHrEmployee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \Application\Entity\NmtHrEmployee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set vendor
     *
     * @param \Application\Entity\NmtBpVendor $vendor
     *
     * @return FinAttachment
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
     * @return FinAttachment
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
     * Set pr
     *
     * @param \Application\Entity\NmtProcurePr $pr
     *
     * @return FinAttachment
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
     * @return FinAttachment
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
}
