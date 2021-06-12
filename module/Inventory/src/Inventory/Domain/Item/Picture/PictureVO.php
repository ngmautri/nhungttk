<?php
namespace Inventory\Domain\Item\Picture;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class PictureVO extends AbstractDTO
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
}