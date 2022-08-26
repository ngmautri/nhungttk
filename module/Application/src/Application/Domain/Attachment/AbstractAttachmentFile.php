<?php
namespace Application\Domain\Attachment;

use Application\Domain\Shared\AbstractEntity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractAttachmentFile extends AbstractEntity
{

    protected $id;

    protected $uuid;

    protected $token;

    protected $fileName;

    protected $fileNameOriginal;

    protected $fileSize;

    protected $filePassword;

    protected $folder;

    protected $relativePath;

    protected $checksum;

    protected $isActive;

    protected $markedForDeletion;

    protected $visibility;

    protected $isPicture;

    protected $isPdf;

    protected $validFrom;

    protected $validTo;

    protected $remarks;

    protected $createdOn;

    protected $lastChangeOn;

    protected $dirName;

    protected $baseName;

    protected $extension;

    protected $mime;

    protected $createdBy;

    protected $lastChangeBy;

    protected $attachment;

    /**
     *
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $fileName
     */
    protected function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     *
     * @param mixed $fileNameOriginal
     */
    protected function setFileNameOriginal($fileNameOriginal)
    {
        $this->fileNameOriginal = $fileNameOriginal;
    }

    /**
     *
     * @param mixed $fileSize
     */
    protected function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     *
     * @param mixed $filePassword
     */
    protected function setFilePassword($filePassword)
    {
        $this->filePassword = $filePassword;
    }

    /**
     *
     * @param mixed $folder
     */
    protected function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     *
     * @param mixed $relativePath
     */
    protected function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;
    }

    /**
     *
     * @param mixed $checksum
     */
    protected function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     *
     * @param mixed $isActive
     */
    protected function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $markedForDeletion
     */
    protected function setMarkedForDeletion($markedForDeletion)
    {
        $this->markedForDeletion = $markedForDeletion;
    }

    /**
     *
     * @param mixed $visibility
     */
    protected function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     *
     * @param mixed $isPicture
     */
    protected function setIsPicture($isPicture)
    {
        $this->isPicture = $isPicture;
    }

    /**
     *
     * @param mixed $isPdf
     */
    protected function setIsPdf($isPdf)
    {
        $this->isPdf = $isPdf;
    }

    /**
     *
     * @param mixed $validFrom
     */
    protected function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;
    }

    /**
     *
     * @param mixed $validTo
     */
    protected function setValidTo($validTo)
    {
        $this->validTo = $validTo;
    }

    /**
     *
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    protected function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $dirName
     */
    protected function setDirName($dirName)
    {
        $this->dirName = $dirName;
    }

    /**
     *
     * @param mixed $baseName
     */
    protected function setBaseName($baseName)
    {
        $this->baseName = $baseName;
    }

    /**
     *
     * @param mixed $extension
     */
    protected function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     *
     * @param mixed $mime
     */
    protected function setMime($mime)
    {
        $this->mime = $mime;
    }

    /**
     *
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $attachment
     */
    protected function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     *
     * @return mixed
     */
    public function getFileNameOriginal()
    {
        return $this->fileNameOriginal;
    }

    /**
     *
     * @return mixed
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     *
     * @return mixed
     */
    public function getFilePassword()
    {
        return $this->filePassword;
    }

    /**
     *
     * @return mixed
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     *
     * @return mixed
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     *
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getMarkedForDeletion()
    {
        return $this->markedForDeletion;
    }

    /**
     *
     * @return mixed
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     *
     * @return mixed
     */
    public function getIsPicture()
    {
        return $this->isPicture;
    }

    /**
     *
     * @return mixed
     */
    public function getIsPdf()
    {
        return $this->isPdf;
    }

    /**
     *
     * @return mixed
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     *
     * @return mixed
     */
    public function getValidTo()
    {
        return $this->validTo;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getDirName()
    {
        return $this->dirName;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseName()
    {
        return $this->baseName;
    }

    /**
     *
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     *
     * @return mixed
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getAttachment()
    {
        return $this->attachment;
    }
}