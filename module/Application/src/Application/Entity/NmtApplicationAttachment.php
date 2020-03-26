<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationAttachment
 *
 * @ORM\Table(name="nmt_application_attachment", indexes={@ORM\Index(name="nmt_application_attachment_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_attachment_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_application_attachment_FK3_idx", columns={"project_id"}), @ORM\Index(name="nmt_application_attachment_FK4_idx", columns={"employee_id"}), @ORM\Index(name="nmt_application_attachment_FK5_idx", columns={"vendor_id"}), @ORM\Index(name="nmt_application_attachment_FK6_idx", columns={"item_purchasing_id"}), @ORM\Index(name="nmt_application_attachment_FK7_idx", columns={"pr_id"}), @ORM\Index(name="nmt_application_attachment_idx1", columns={"target_class"}), @ORM\Index(name="nmt_application_attachment_idx2", columns={"target_id"}), @ORM\Index(name="nmt_application_attachment_idx3", columns={"target_token"}), @ORM\Index(name="nmt_application_attachment_FK9_idx", columns={"v_invoice_id"}), @ORM\Index(name="nmt_application_attachment_FK10_idx", columns={"item_id"}), @ORM\Index(name="nmt_application_attachment_FK11_idx", columns={"po_id"}), @ORM\Index(name="nmt_application_attachment_FK12_idx", columns={"po_row_id"}), @ORM\Index(name="nmt_application_attachment_FK13_idx", columns={"qo_id"}), @ORM\Index(name="nmt_application_attachment_FK14_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtApplicationAttachment
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
     * @var string
     *
     * @ORM\Column(name="target_class", type="string", length=100, nullable=true)
     */
    private $targetClass;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_id", type="integer", nullable=true)
     */
    private $targetId;

    /**
     * @var string
     *
     * @ORM\Column(name="target_token", type="string", length=45, nullable=true)
     */
    private $targetToken;

    /**
     * @var string
     *
     * @ORM\Column(name="file_extension", type="string", length=5, nullable=true)
     */
    private $fileExtension;

    /**
     * @var boolean
     *
     * @ORM\Column(name="file_exits", type="boolean", nullable=true)
     */
    private $fileExits;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

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
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;

    /**
     * @var \Application\Entity\NmtProcurePo
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_id", referencedColumnName="id")
     * })
     */
    private $po;

    /**
     * @var \Application\Entity\NmtProcurePoRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePoRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_row_id", referencedColumnName="id")
     * })
     */
    private $poRow;

    /**
     * @var \Application\Entity\NmtProcureQo
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureQo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="qo_id", referencedColumnName="id")
     * })
     */
    private $qo;

    /**
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

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
     * @var \Application\Entity\FinVendorInvoice
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoice")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="v_invoice_id", referencedColumnName="id")
     * })
     */
    private $vInvoice;



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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * Set targetClass
     *
     * @param string $targetClass
     *
     * @return NmtApplicationAttachment
     */
    public function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;

        return $this;
    }

    /**
     * Get targetClass
     *
     * @return string
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     * Set targetId
     *
     * @param integer $targetId
     *
     * @return NmtApplicationAttachment
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * Get targetId
     *
     * @return integer
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * Set targetToken
     *
     * @param string $targetToken
     *
     * @return NmtApplicationAttachment
     */
    public function setTargetToken($targetToken)
    {
        $this->targetToken = $targetToken;

        return $this;
    }

    /**
     * Get targetToken
     *
     * @return string
     */
    public function getTargetToken()
    {
        return $this->targetToken;
    }

    /**
     * Set fileExtension
     *
     * @param string $fileExtension
     *
     * @return NmtApplicationAttachment
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Get fileExtension
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Set fileExits
     *
     * @param boolean $fileExits
     *
     * @return NmtApplicationAttachment
     */
    public function setFileExits($fileExits)
    {
        $this->fileExits = $fileExits;

        return $this;
    }

    /**
     * Get fileExits
     *
     * @return boolean
     */
    public function getFileExits()
    {
        return $this->fileExits;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtApplicationAttachment
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtApplicationAttachment
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
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtApplicationAttachment
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
     * Set po
     *
     * @param \Application\Entity\NmtProcurePo $po
     *
     * @return NmtApplicationAttachment
     */
    public function setPo(\Application\Entity\NmtProcurePo $po = null)
    {
        $this->po = $po;

        return $this;
    }

    /**
     * Get po
     *
     * @return \Application\Entity\NmtProcurePo
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     * Set poRow
     *
     * @param \Application\Entity\NmtProcurePoRow $poRow
     *
     * @return NmtApplicationAttachment
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
     * Set qo
     *
     * @param \Application\Entity\NmtProcureQo $qo
     *
     * @return NmtApplicationAttachment
     */
    public function setQo(\Application\Entity\NmtProcureQo $qo = null)
    {
        $this->qo = $qo;

        return $this;
    }

    /**
     * Get qo
     *
     * @return \Application\Entity\NmtProcureQo
     */
    public function getQo()
    {
        return $this->qo;
    }

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtApplicationAttachment
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * @return NmtApplicationAttachment
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
     * Set vInvoice
     *
     * @param \Application\Entity\FinVendorInvoice $vInvoice
     *
     * @return NmtApplicationAttachment
     */
    public function setVInvoice(\Application\Entity\FinVendorInvoice $vInvoice = null)
    {
        $this->vInvoice = $vInvoice;

        return $this;
    }

    /**
     * Get vInvoice
     *
     * @return \Application\Entity\FinVendorInvoice
     */
    public function getVInvoice()
    {
        return $this->vInvoice;
    }
}
