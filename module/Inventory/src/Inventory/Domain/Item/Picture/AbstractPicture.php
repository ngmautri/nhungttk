<?php
namespace Inventory\Domain\Item\Picture;

use Application\Domain\Shared\AbstractEntity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractPicture extends AbstractEntity
{

    protected $id;

    protected $documentSubject;

    protected $url;

    protected $filename;

    protected $originalFilename;

    protected $filetype;

    protected $size;

    protected $visibility;

    protected $folder;

    protected $folderRelative;

    protected $checksum;

    protected $token;

    protected $remarks;

    protected $isDefault;

    protected $isActive;

    protected $markedForDeletion;

    protected $createdOn;

    protected $fileExits;

    protected $createdBy;

    protected $item;

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
    public function getDocumentSubject()
    {
        return $this->documentSubject;
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
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
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
     * @return mixed
     */
    public function getFiletype()
    {
        return $this->filetype;
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
    public function getFolder()
    {
        return $this->folder;
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
    public function getToken()
    {
        return $this->token;
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
    public function getIsDefault()
    {
        return $this->isDefault;
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
    public function getCreatedOn()
    {
        return $this->createdOn;
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
    public function getItem()
    {
        return $this->item;
    }

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
     * @param mixed $documentSubject
     */
    protected function setDocumentSubject($documentSubject)
    {
        $this->documentSubject = $documentSubject;
    }

    /**
     *
     * @param mixed $url
     */
    protected function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @param mixed $filename
     */
    protected function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     *
     * @param mixed $originalFilename
     */
    protected function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;
    }

    /**
     *
     * @param mixed $filetype
     */
    protected function setFiletype($filetype)
    {
        $this->filetype = $filetype;
    }

    /**
     *
     * @param mixed $size
     */
    protected function setSize($size)
    {
        $this->size = $size;
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
     * @param mixed $folder
     */
    protected function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     *
     * @param mixed $folderRelative
     */
    protected function setFolderRelative($folderRelative)
    {
        $this->folderRelative = $folderRelative;
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
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
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
     * @param mixed $isDefault
     */
    protected function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
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
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $fileExits
     */
    protected function setFileExits($fileExits)
    {
        $this->fileExits = $fileExits;
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
     * @param mixed $item
     */
    protected function setItem($item)
    {
        $this->item = $item;
    }
}
