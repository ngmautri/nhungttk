<?php
namespace Application\Domain\Attachment;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentFileSnapshot extends AbstractDTO
{

    /*
     * |=============================
     * | Application\Domain\Attachment\AbstractAttachmentFile
     * |
     * |=============================
     */
    public $id;

    public $uuid;

    public $token;

    public $fileName;

    public $fileNameOriginal;

    public $fileSize;

    public $filePassword;

    public $folder;

    public $relativePath;

    public $checksum;

    public $isActive;

    public $markedForDeletion;

    public $visibility;

    public $isPicture;

    public $isPdf;

    public $validFrom;

    public $validTo;

    public $remarks;

    public $createdOn;

    public $lastChangeOn;

    public $dirName;

    public $baseName;

    public $extension;

    public $mime;

    public $createdBy;

    public $lastChangeBy;

    public $attachment;

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

    /**
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     *
     * @param mixed $fileNameOriginal
     */
    public function setFileNameOriginal($fileNameOriginal)
    {
        $this->fileNameOriginal = $fileNameOriginal;
    }

    /**
     *
     * @param mixed $fileSize
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     *
     * @param mixed $filePassword
     */
    public function setFilePassword($filePassword)
    {
        $this->filePassword = $filePassword;
    }

    /**
     *
     * @param mixed $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     *
     * @param mixed $relativePath
     */
    public function setRelativePath($relativePath)
    {
        $this->relativePath = $relativePath;
    }

    /**
     *
     * @param mixed $checksum
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     *
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $markedForDeletion
     */
    public function setMarkedForDeletion($markedForDeletion)
    {
        $this->markedForDeletion = $markedForDeletion;
    }

    /**
     *
     * @param mixed $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     *
     * @param mixed $isPicture
     */
    public function setIsPicture($isPicture)
    {
        $this->isPicture = $isPicture;
    }

    /**
     *
     * @param mixed $isPdf
     */
    public function setIsPdf($isPdf)
    {
        $this->isPdf = $isPdf;
    }

    /**
     *
     * @param mixed $validFrom
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;
    }

    /**
     *
     * @param mixed $validTo
     */
    public function setValidTo($validTo)
    {
        $this->validTo = $validTo;
    }

    /**
     *
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $dirName
     */
    public function setDirName($dirName)
    {
        $this->dirName = $dirName;
    }

    /**
     *
     * @param mixed $baseName
     */
    public function setBaseName($baseName)
    {
        $this->baseName = $baseName;
    }

    /**
     *
     * @param mixed $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     *
     * @param mixed $mime
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
    }

    /**
     *
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }
}