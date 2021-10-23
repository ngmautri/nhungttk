<?php
namespace Procure\Application\DTO\Po;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoDocMapDTO
{

    public $poId;

    public $poSysNumber;

    public $docType;

    public $docId;

    public $docToken;

    public $docSysNumber;

    public $docCurrency;

    public $docNetAmount;

    public $localNetAmount;

    public $docPostingDate;

    public $docDate;

    public $docCreatedDate;

    /**
     *
     * @return mixed
     */
    public function getPoId()
    {
        return $this->poId;
    }

    /**
     *
     * @param mixed $poId
     */
    public function setPoId($poId)
    {
        $this->poId = $poId;
    }

    /**
     *
     * @return mixed
     */
    public function getPoSysNumber()
    {
        return $this->poSysNumber;
    }

    /**
     *
     * @param mixed $poSysNumber
     */
    public function setPoSysNumber($poSysNumber)
    {
        $this->poSysNumber = $poSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     *
     * @param mixed $docType
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;
    }

    /**
     *
     * @return mixed
     */
    public function getDocId()
    {
        return $this->docId;
    }

    /**
     *
     * @param mixed $docId
     */
    public function setDocId($docId)
    {
        $this->docId = $docId;
    }

    /**
     *
     * @return mixed
     */
    public function getDocToken()
    {
        return $this->docToken;
    }

    /**
     *
     * @param mixed $docToken
     */
    public function setDocToken($docToken)
    {
        $this->docToken = $docToken;
    }

    /**
     *
     * @return mixed
     */
    public function getDocSysNumber()
    {
        return $this->docSysNumber;
    }

    /**
     *
     * @param mixed $docSysNumber
     */
    public function setDocSysNumber($docSysNumber)
    {
        $this->docSysNumber = $docSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrency()
    {
        return $this->docCurrency;
    }

    /**
     *
     * @param mixed $docCurrency
     */
    public function setDocCurrency($docCurrency)
    {
        $this->docCurrency = $docCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getDocNetAmount()
    {
        return $this->docNetAmount;
    }

    /**
     *
     * @param mixed $docNetAmount
     */
    public function setDocNetAmount($docNetAmount)
    {
        $this->docNetAmount = $docNetAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalNetAmount()
    {
        return $this->localNetAmount;
    }

    /**
     *
     * @param mixed $localNetAmount
     */
    public function setLocalNetAmount($localNetAmount)
    {
        $this->localNetAmount = $localNetAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getDocPostingDate()
    {
        return $this->docPostingDate;
    }

    /**
     *
     * @param mixed $docPostingDate
     */
    public function setDocPostingDate($docPostingDate)
    {
        $this->docPostingDate = $docPostingDate;
    }

    /**
     *
     * @return mixed
     */
    public function getDocDate()
    {
        return $this->docDate;
    }

    /**
     *
     * @param mixed $docDate
     */
    public function setDocDate($docDate)
    {
        $this->docDate = $docDate;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCreatedDate()
    {
        return $this->docCreatedDate;
    }

    /**
     *
     * @param mixed $docCreatedDate
     */
    public function setDocCreatedDate($docCreatedDate)
    {
        $this->docCreatedDate = $docCreatedDate;
    }
}

