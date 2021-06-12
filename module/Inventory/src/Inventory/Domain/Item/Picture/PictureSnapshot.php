<?php
namespace Inventory\Domain\Item\Picture;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PictureSnapshot extends AbstractDTO
{

    public $id;

    public $documentSubject;

    public $url;

    public $filename;

    public $originalFilename;

    public $filetype;

    public $size;

    public $visibility;

    public $folder;

    public $folderRelative;

    public $checksum;

    public $token;

    public $remarks;

    public $isDefault;

    public $isActive;

    public $markedForDeletion;

    public $createdOn;

    public $fileExits;

    public $createdBy;

    public $item;

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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return mixed
     */
    public function getDocumentSubject()
    {
        return $this->documentSubject;
    }

    /**
     *
     * @param mixed $documentSubject
     */
    public function setDocumentSubject($documentSubject)
    {
        $this->documentSubject = $documentSubject;
    }

    /**
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     *
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     *
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     *
     * @return mixed
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     *
     * @param mixed $originalFilename
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;
    }

    /**
     *
     * @return mixed
     */
    public function getFiletype()
    {
        return $this->filetype;
    }

    /**
     *
     * @param mixed $filetype
     */
    public function setFiletype($filetype)
    {
        $this->filetype = $filetype;
    }

    /**
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     *
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
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
     * @param mixed $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
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
     * @param mixed $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     *
     * @return mixed
     */
    public function getFolderRelative()
    {
        return $this->folderRelative;
    }

    /**
     *
     * @param mixed $folderRelative
     */
    public function setFolderRelative($folderRelative)
    {
        $this->folderRelative = $folderRelative;
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
     * @param mixed $checksum
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
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
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
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
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     *
     * @param mixed $isDefault
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
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
     * @param mixed $markedForDeletion
     */
    public function setMarkedForDeletion($markedForDeletion)
    {
        $this->markedForDeletion = $markedForDeletion;
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
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getFileExits()
    {
        return $this->fileExits;
    }

    /**
     *
     * @param mixed $fileExits
     */
    public function setFileExits($fileExits)
    {
        $this->fileExits = $fileExits;
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
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     *
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }
}