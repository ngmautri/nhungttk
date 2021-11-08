<?php
namespace Application\Application\Helper\ParamQuery\Contracts;

/**
 * Abstract ParamQuery
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractParamQueryDataModel
{

    const CONTENT_TYPE = 'application/json; charset=UTF-8';

    const TEXT = 'text';

    const XML = 'xml';

    const JSON = 'json';

    const LOCAL = 'local';

    const REMOTE = 'remote';

    const GET = 'get';

    const POST = 'post';

    protected $beforeSend;

    protected $contentType;

    protected $data;

    protected $dataType;

    protected $error;

    protected $getData;

    protected $getUrl;

    protected $location;

    protected $method;

    protected $postData;

    protected $postDataOnce;

    protected $recIndx;

    protected $sortDir;

    protected $sortIndx;

    protected $sorting;

    protected $url;

    /**
     *
     * @return mixed
     */
    public function getBeforeSend()
    {
        return $this->beforeSend;
    }

    /**
     *
     * @param mixed $beforeSend
     */
    public function setBeforeSend($beforeSend)
    {
        $this->beforeSend = \sprintf("beforeSend: \"%s\"", $beforeSend);
        return $this->beforeSend;
    }

    /**
     *
     * @return mixed
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     *
     * @param mixed $contentType
     */
    public function setContentType($contentType = self::CONTENT_TYPE)
    {
        $this->contentType = \sprintf("contentType: \"%s\"", $contentType);
        return $this->contentType;
    }

    /**
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     *
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = \sprintf("data: \"%s\"", $data);
        return $this->data;
    }

    /**
     *
     * @return mixed
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     *
     * @param mixed $dataType
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
    }

    /**
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     *
     * @param mixed $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     *
     * @return mixed
     */
    public function getGetData()
    {
        return $this->getData;
    }

    /**
     *
     * @param mixed $getData
     */
    public function setGetData($getData)
    {
        $this->getData = \sprintf("getData: \"%s\"", $getData);
        return $this->getData;
    }

    /**
     *
     * @return mixed
     */
    public function getGetUrl()
    {
        return $this->getUrl;
    }

    /**
     *
     * @param mixed $getUrl
     */
    public function setGetUrl($getUrl)
    {
        $this->getUrl = $getUrl;
    }

    /**
     *
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     *
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = \sprintf("location: \"%s\"", $location);
        return $this->location;
    }

    /**
     *
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     *
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = \sprintf("method: \"%s\"", $method);
        return $this->method;
    }

    /**
     *
     * @return mixed
     */
    public function getPostData()
    {
        return $this->postData;
    }

    /**
     *
     * @param mixed $postData
     */
    public function setPostData($postData)
    {
        $this->postData = \sprintf("postData: \"%s\"", $postData);
        return $this->postData;
    }

    /**
     *
     * @return mixed
     */
    public function getPostDataOnce()
    {
        return $this->postDataOnce;
    }

    /**
     *
     * @param mixed $postDataOnce
     */
    public function setPostDataOnce($postDataOnce)
    {
        $this->postDataOnce = $postDataOnce;
    }

    /**
     *
     * @return mixed
     */
    public function getRecIndx()
    {
        return $this->recIndx;
    }

    /**
     *
     * @param mixed $recIndx
     */
    public function setRecIndx($recIndx)
    {
        $this->recIndx = $recIndx;
    }

    /**
     *
     * @return mixed
     */
    public function getSortDir()
    {
        return $this->sortDir;
    }

    /**
     *
     * @param mixed $sortDir
     */
    public function setSortDir($sortDir)
    {
        $this->sortDir = $sortDir;
    }

    /**
     *
     * @return mixed
     */
    public function getSortIndx()
    {
        return $this->sortIndx;
    }

    /**
     *
     * @param mixed $sortIndx
     */
    public function setSortIndx($sortIndx)
    {
        $this->sortIndx = $sortIndx;
    }

    /**
     *
     * @return mixed
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     *
     * @param mixed $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = \sprintf("sorting: \"%s\"", $sorting);
        return $this->sorting;
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
        $this->url = \sprintf("url: \"%s\"", $url);
        return $this->url;
    }
}

