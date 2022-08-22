<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrAttachmentFile
 *
 * @ORM\Table(name="hr_attachment_file", indexes={@ORM\Index(name="hr_attachment_FK1_idx", columns={"created_by"}), @ORM\Index(name="hr_attachment_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="hr_attachment_file_FK3_idx", columns={"attachment_id"})})
 * @ORM\Entity
 */
class HrAttachmentFile
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
     * @ORM\Column(name="uuid", type="string", length=45, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=50, nullable=true)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name_original", type="string", length=100, nullable=true)
     */
    private $fileNameOriginal;

    /**
     * @var string
     *
     * @ORM\Column(name="file_size", type="string", length=45, nullable=true)
     */
    private $fileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="file_password", type="string", length=45, nullable=true)
     */
    private $filePassword;

    /**
     * @var string
     *
     * @ORM\Column(name="folder", type="string", length=255, nullable=true)
     */
    private $folder;

    /**
     * @var string
     *
     * @ORM\Column(name="relative_path", type="string", length=255, nullable=true)
     */
    private $relativePath;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=100, nullable=true)
     */
    private $checksum;

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
     * @var boolean
     *
     * @ORM\Column(name="visibility", type="boolean", nullable=true)
     */
    private $visibility;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_picture", type="boolean", nullable=true)
     */
    private $isPicture;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_pdf", type="boolean", nullable=true)
     */
    private $isPdf;

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
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="dir_name", type="string", length=255, nullable=true)
     */
    private $dirName;

    /**
     * @var string
     *
     * @ORM\Column(name="base_name", type="string", length=45, nullable=true)
     */
    private $baseName;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=10, nullable=true)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="mime", type="string", length=100, nullable=true)
     */
    private $mime;

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
     * @var \Application\Entity\HrAttachment
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\HrAttachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="attachment_id", referencedColumnName="id")
     * })
     */
    private $attachment;



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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return HrAttachmentFile
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
     * Set token
     *
     * @param string $token
     *
     * @return HrAttachmentFile
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
     * Set fileName
     *
     * @param string $fileName
     *
     * @return HrAttachmentFile
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set fileNameOriginal
     *
     * @param string $fileNameOriginal
     *
     * @return HrAttachmentFile
     */
    public function setFileNameOriginal($fileNameOriginal)
    {
        $this->fileNameOriginal = $fileNameOriginal;

        return $this;
    }

    /**
     * Get fileNameOriginal
     *
     * @return string
     */
    public function getFileNameOriginal()
    {
        return $this->fileNameOriginal;
    }

    /**
     * Set fileSize
     *
     * @param string $fileSize
     *
     * @return HrAttachmentFile
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize
     *
     * @return string
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set filePassword
     *
     * @param string $filePassword
     *
     * @return HrAttachmentFile
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
     * Set folder
     *
     * @param string $folder
     *
     * @return HrAttachmentFile
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
     * Set relativePath
     *
     * @param string $relativePath
     *
     * @return HrAttachmentFile
     */
    public function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;

        return $this;
    }

    /**
     * Get relativePath
     *
     * @return string
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     *
     * @return HrAttachmentFile
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return HrAttachmentFile
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
     * @return HrAttachmentFile
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
     * Set visibility
     *
     * @param boolean $visibility
     *
     * @return HrAttachmentFile
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
     * Set isPicture
     *
     * @param boolean $isPicture
     *
     * @return HrAttachmentFile
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
     * Set isPdf
     *
     * @param boolean $isPdf
     *
     * @return HrAttachmentFile
     */
    public function setIsPdf($isPdf)
    {
        $this->isPdf = $isPdf;

        return $this;
    }

    /**
     * Get isPdf
     *
     * @return boolean
     */
    public function getIsPdf()
    {
        return $this->isPdf;
    }

    /**
     * Set validFrom
     *
     * @param \DateTime $validFrom
     *
     * @return HrAttachmentFile
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
     * @return HrAttachmentFile
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return HrAttachmentFile
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
     * @return HrAttachmentFile
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
     * @return HrAttachmentFile
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
     * Set dirName
     *
     * @param string $dirName
     *
     * @return HrAttachmentFile
     */
    public function setDirName($dirName)
    {
        $this->dirName = $dirName;

        return $this;
    }

    /**
     * Get dirName
     *
     * @return string
     */
    public function getDirName()
    {
        return $this->dirName;
    }

    /**
     * Set baseName
     *
     * @param string $baseName
     *
     * @return HrAttachmentFile
     */
    public function setBaseName($baseName)
    {
        $this->baseName = $baseName;

        return $this;
    }

    /**
     * Get baseName
     *
     * @return string
     */
    public function getBaseName()
    {
        return $this->baseName;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return HrAttachmentFile
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set mime
     *
     * @param string $mime
     *
     * @return HrAttachmentFile
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get mime
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return HrAttachmentFile
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
     * @return HrAttachmentFile
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
     * Set attachment
     *
     * @param \Application\Entity\HrAttachment $attachment
     *
     * @return HrAttachmentFile
     */
    public function setAttachment(\Application\Entity\HrAttachment $attachment = null)
    {
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * Get attachment
     *
     * @return \Application\Entity\HrAttachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }
}
