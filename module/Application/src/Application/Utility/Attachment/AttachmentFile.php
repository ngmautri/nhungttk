<?php
namespace Application\Utility\Attachment;

class AttachmentFile
{

    private $fileName;

    private $fileSize;

    private $fileTmp;

    private $fileType;

    private $fileExtension;

    /**
     *
     * @return the $fileName
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     *
     * @return the $fileSize
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     *
     * @return the $fileTmp
     */
    public function getFileTmp()
    {
        return $this->fileTmp;
    }

    /**
     *
     * @return the $fileType
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     *
     * @return the $fileExtension
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     *
     * @param field_type $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     *
     * @param field_type $fileSize
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     *
     * @param field_type $fileTmp
     */
    public function setFileTmp($fileTmp)
    {
        $this->fileTmp = $fileTmp;
    }

    /**
     *
     * @param field_type $fileType
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
    }

    /**
     *
     * @param field_type $fileExtension
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }
}