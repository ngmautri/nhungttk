<?php
namespace Application\Domain\Attachment;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentSnapshot extends AbstractValueObject
{

    public $id;

    public $documentSubject;

    public $keywords;

    public $isPicture;

    public $isContract;

    public $signingDate;

    public $validFrom;

    public $validTo;

    public $url;

    public $filetype;

    public $filename;

    public $filenameOriginal;

    public $filePassword;

    public $size;

    public $visibility;

    public $folder;

    public $attachmentFolder;

    public $folderRelative;

    public $checksum;

    public $token;

    public $isActive;

    public $markedForDeletion;

    public $remarks;

    public $createdOn;

    public $lastChangeOn;

    public $changeFor;

    public $prRowId;

    public $targetClass;

    public $targetId;

    public $targetToken;

    public $fileExtension;

    public $fileExits;

    public $uuid;

    public $createdBy;

    public $item;

    public $po;

    public $poRow;

    public $qo;

    public $company;

    public $lastChangeBy;

    public $project;

    public $employee;

    public $vendor;

    public $itemPurchasing;

    public $pr;

    public $vInvoice;

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
    public function getKeywords()
    {
        return $this->keywords;
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
    public function getIsContract()
    {
        return $this->isContract;
    }

    /**
     *
     * @return mixed
     */
    public function getSigningDate()
    {
        return $this->signingDate;
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
    public function getUrl()
    {
        return $this->url;
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
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     *
     * @return mixed
     */
    public function getFilenameOriginal()
    {
        return $this->filenameOriginal;
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
    public function getAttachmentFolder()
    {
        return $this->attachmentFolder;
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
    public function getChangeFor()
    {
        return $this->changeFor;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowId()
    {
        return $this->prRowId;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetToken()
    {
        return $this->targetToken;
    }

    /**
     *
     * @return mixed
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
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
    public function getUuid()
    {
        return $this->uuid;
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
     * @return mixed
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     *
     * @return mixed
     */
    public function getPoRow()
    {
        return $this->poRow;
    }

    /**
     *
     * @return mixed
     */
    public function getQo()
    {
        return $this->qo;
    }

    /**
     *
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
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
    public function getProject()
    {
        return $this->project;
    }

    /**
     *
     * @return mixed
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     *
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     *
     * @return mixed
     */
    public function getItemPurchasing()
    {
        return $this->itemPurchasing;
    }

    /**
     *
     * @return mixed
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     *
     * @return mixed
     */
    public function getVInvoice()
    {
        return $this->vInvoice;
    }
}