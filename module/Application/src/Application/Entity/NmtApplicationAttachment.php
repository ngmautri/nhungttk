<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationAttachment
 *
 * @ORM\Table(name="nmt_application_attachment", indexes={@ORM\Index(name="nmt_application_attachment_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_attachment_FK2_idx", columns={"last_changed_by"}), @ORM\Index(name="nmt_application_attachment_FK3_idx", columns={"project_id"}), @ORM\Index(name="nmt_application_attachment_FK4_idx", columns={"employee_id"}), @ORM\Index(name="nmt_application_attachment_FK5_idx", columns={"vendor_id"})})
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
     * @ORM\Column(name="document_type", type="string", length=45, nullable=false)
     */
    private $documentType;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=150, nullable=true)
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
     * @ORM\Column(name="filename_original", type="string", length=150, nullable=true)
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
     * @ORM\Column(name="folder", type="string", length=105, nullable=true)
     */
    private $folder;

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
     * @var boolean
     *
     * @ORM\Column(name="marked_for_deletion", type="boolean", nullable=true)
     */
    private $markedForDeletion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

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
     *   @ORM\JoinColumn(name="last_changed_by", referencedColumnName="id")
     * })
     */
    private $lastChangedBy;

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
     * @return NmtApplicationAttachment
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
     * Set itemId
     *
     * @param integer $itemId
     *
     * @return NmtApplicationAttachment
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return integer
     */
    public function getItemId()
    {
        return $this->itemId;
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
     * Set lastChangedBy
     *
     * @param \Application\Entity\MlaUsers $lastChangedBy
     *
     * @return NmtApplicationAttachment
     */
    public function setLastChangedBy(\Application\Entity\MlaUsers $lastChangedBy = null)
    {
        $this->lastChangedBy = $lastChangedBy;

        return $this;
    }

    /**
     * Get lastChangedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastChangedBy()
    {
        return $this->lastChangedBy;
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
}
