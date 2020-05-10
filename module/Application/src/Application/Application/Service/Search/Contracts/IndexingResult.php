<?php
namespace Application\Application\Service\Search\Contracts;

/**
 * Search Result
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IndexingResult
{

    protected $message;

    protected $indexSize;

    protected $indexDirectory;

    protected $fileList;

    protected $indexVesion;

    protected $docsCount;

    protected $hasDeletion;

    protected $isSuccess;

    /**
     *
     * @return mixed
     */
    public function getFileList()
    {
        return $this->fileList;
    }

    /**
     *
     * @param mixed $fileList
     */
    public function setFileList($fileList)
    {
        $this->fileList = $fileList;
    }

    /**
     *
     * @return mixed
     */
    public function getIsSuccess()
    {
        return $this->isSuccess;
    }

    /**
     *
     * @param mixed $isSuccess
     */
    public function setIsSuccess($isSuccess)
    {
        $this->isSuccess = $isSuccess;
    }

    /**
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     *
     * @return mixed
     */
    public function getIndexSize()
    {
        return $this->indexSize;
    }

    /**
     *
     * @return mixed
     */
    public function getIndexDirectory()
    {
        return $this->indexDirectory;
    }

    /**
     *
     * @return mixed
     */
    public function getIndexVesion()
    {
        return $this->indexVesion;
    }

    /**
     *
     * @return mixed
     */
    public function getDocsCount()
    {
        return $this->docsCount;
    }

    /**
     *
     * @return mixed
     */
    public function getHasDeletion()
    {
        return $this->hasDeletion;
    }

    /**
     *
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     *
     * @param mixed $indexSize
     */
    public function setIndexSize($indexSize)
    {
        $this->indexSize = $indexSize;
    }

    /**
     *
     * @param mixed $indexDirectory
     */
    public function setIndexDirectory($indexDirectory)
    {
        $this->indexDirectory = $indexDirectory;
    }

    /**
     *
     * @param mixed $indexVesion
     */
    public function setIndexVesion($indexVesion)
    {
        $this->indexVesion = $indexVesion;
    }

    /**
     *
     * @param mixed $docsCount
     */
    public function setDocsCount($docsCount)
    {
        $this->docsCount = $docsCount;
    }

    /**
     *
     * @param mixed $hasDeletion
     */
    public function setHasDeletion($hasDeletion)
    {
        $this->hasDeletion = $hasDeletion;
    }

    public function __construct()
    {}
}
