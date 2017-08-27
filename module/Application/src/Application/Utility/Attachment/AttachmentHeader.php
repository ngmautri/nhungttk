<?php

namespace Application\Utility\Attachment;

class AttachmentHeader
{
    private $documentSubject;
    private $validFrom; 
    private $validTo;
    private $validFrom; 
    
    private $isActive; 
    private $markedForDeletion; 
    private $filePassword; 
    private $visibility;
    /**
     * @return the $documentSubject
     */
    public function getDocumentSubject()
    {
        return $this->documentSubject;
    }

    /**
     * @return the $validFrom
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * @return the $validTo
     */
    public function getValidTo()
    {
        return $this->validTo;
    }

    /**
     * @return the $validFrom
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * @return the $isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return the $markedForDeletion
     */
    public function getMarkedForDeletion()
    {
        return $this->markedForDeletion;
    }

    /**
     * @return the $filePassword
     */
    public function getFilePassword()
    {
        return $this->filePassword;
    }

    /**
     * @return the $visibility
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param field_type $documentSubject
     */
    public function setDocumentSubject($documentSubject)
    {
        $this->documentSubject = $documentSubject;
    }

    /**
     * @param field_type $validFrom
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;
    }

    /**
     * @param field_type $validTo
     */
    public function setValidTo($validTo)
    {
        $this->validTo = $validTo;
    }

    /**
     * @param field_type $validFrom
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;
    }

    /**
     * @param field_type $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @param field_type $markedForDeletion
     */
    public function setMarkedForDeletion($markedForDeletion)
    {
        $this->markedForDeletion = $markedForDeletion;
    }

    /**
     * @param field_type $filePassword
     */
    public function setFilePassword($filePassword)
    {
        $this->filePassword = $filePassword;
    }

    /**
     * @param field_type $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

   
    
}