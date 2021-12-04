<?php
namespace Procure\Domain;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Abstract Procure Document.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class BaseDoc extends AbstractDoc
{

    private $generatorInjected;

    protected $rowsGenerator;

    protected $lazyRowSnapshotCollection;

    protected $lazyRowSnapshotCollectionReference;

    protected $rowCollection;

    // Addtional attributes
    // =========================
    protected $pictureList;

    protected $attachmentList;

    protected $totalPicture;

    protected $totalAttachment;

    protected $docRows;

    protected $rowIdArray;

    protected $rowsOutput;

    // Posting Date
    // =========================
    protected $postingYear;

    protected $postingMonth;

    protected $docYear;

    protected $docMonth;

    protected $postingPeriodFrom;

    protected $postingPeriodTo;

    protected $postingPeriodId;

    // Company
    // =========================
    protected $companyName;

    protected $companyId;

    protected $companyToken;

    protected $companyCode;

    // Vendor Details
    // =========================
    protected $vendorId;

    protected $vendorToken;

    protected $vendorAddress;

    protected $vendorCountry;

    // Doc Details
    // =========================
    protected $completedAPRows;

    protected $completedGRRows;

    protected $paymentTermName;

    protected $paymentTermCode;

    protected $warehouseName;

    protected $warehouseCode;

    protected $paymentMethodName;

    protected $paymentMethodCode;

    protected $incotermCode;

    protected $incotermName;

    protected $createdByName;

    protected $lastChangedByName;

    protected $totalRows;

    protected $totalActiveRows;

    protected $maxRowNumber;

    protected $netAmount;

    protected $taxAmount;

    protected $grossAmount;

    protected $discountAmount;

    protected $billedAmount;

    protected $completedRows;

    protected $openAPAmount;

    protected $docCurrencyISO;

    protected $localCurrencyISO;

    protected $docCurrencyId;

    protected $localCurrencyId;

    /*
     * |=============================
     * | Getter Setter
     * |
     * |=============================
     */

    /**
     *
     * @return mixed
     */
    public function getGeneratorInjected()
    {
        return $this->generatorInjected;
    }

    /**
     *
     * @param mixed $generatorInjected
     */
    private function setGeneratorInjected($generatorInjected)
    {
        $this->generatorInjected = $generatorInjected;
    }

    /**
     *
     * @return \Generator
     */
    public function getRowsGenerator()
    {
        return $this->rowsGenerator;
    }

    /**
     *
     * @param \Generator $rowsGenerator
     */
    public function setRowsGenerator(\Generator $rowsGenerator = null)
    {
        if ($this->getGeneratorInjected()) {
            return;
        }
        $this->rowsGenerator = $rowsGenerator;

        $this->generatorInjected = true;
    }

    public function setLazyRowSnapshotCollectionReference($lazyRowSnapshotCollectionReference)
    {
        $this->lazyRowSnapshotCollectionReference = $lazyRowSnapshotCollectionReference;
    }

    /**
     *
     * @return NULL|ArrayCollection
     */
    public function getLazyRowSnapshotCollection()
    {
        $ref = $this->getLazyRowSnapshotCollectionReference();
        if ($ref == null) {
            return null;
        }

        $this->lazyRowSnapshotCollection = $ref();
        return $this->lazyRowSnapshotCollection;
    }

    public function getDocRowsCount()
    {
        return count($this->getDocRows());
    }

    /**
     *
     * @return mixed
     */
    public function getPictureList()
    {
        return $this->pictureList;
    }

    /**
     *
     * @return mixed
     */
    public function getAttachmentList()
    {
        return $this->attachmentList;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalPicture()
    {
        return $this->totalPicture;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalAttachment()
    {
        return $this->totalAttachment;
    }

    /**
     *
     * @return mixed
     */
    public function getDocRows()
    {
        return $this->docRows;
    }

    /**
     *
     * @return mixed
     */
    public function getRowIdArray()
    {
        return $this->rowIdArray;
    }

    /**
     *
     * @return mixed
     */
    public function getRowsOutput()
    {
        return $this->rowsOutput;
    }

    /**
     *
     * @return mixed
     */
    public function getPostingYear()
    {
        return $this->postingYear;
    }

    /**
     *
     * @return mixed
     */
    public function getPostingMonth()
    {
        return $this->postingMonth;
    }

    /**
     *
     * @return mixed
     */
    public function getDocYear()
    {
        return $this->docYear;
    }

    /**
     *
     * @return mixed
     */
    public function getDocMonth()
    {
        return $this->docMonth;
    }

    /**
     *
     * @return mixed
     */
    public function getPostingPeriodFrom()
    {
        return $this->postingPeriodFrom;
    }

    /**
     *
     * @return mixed
     */
    public function getPostingPeriodTo()
    {
        return $this->postingPeriodTo;
    }

    /**
     *
     * @return mixed
     */
    public function getPostingPeriodId()
    {
        return $this->postingPeriodId;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyToken()
    {
        return $this->companyToken;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorToken()
    {
        return $this->vendorToken;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorAddress()
    {
        return $this->vendorAddress;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorCountry()
    {
        return $this->vendorCountry;
    }

    /**
     *
     * @return mixed
     */
    public function getCompletedAPRows()
    {
        return $this->completedAPRows;
    }

    /**
     *
     * @return mixed
     */
    public function getCompletedGRRows()
    {
        return $this->completedGRRows;
    }

    /**
     *
     * @return mixed
     */
    public function getPaymentTermName()
    {
        return $this->paymentTermName;
    }

    /**
     *
     * @return mixed
     */
    public function getPaymentTermCode()
    {
        return $this->paymentTermCode;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouseCode()
    {
        return $this->warehouseCode;
    }

    /**
     *
     * @return mixed
     */
    public function getPaymentMethodName()
    {
        return $this->paymentMethodName;
    }

    /**
     *
     * @return mixed
     */
    public function getPaymentMethodCode()
    {
        return $this->paymentMethodCode;
    }

    /**
     *
     * @return mixed
     */
    public function getIncotermCode()
    {
        return $this->incotermCode;
    }

    /**
     *
     * @return mixed
     */
    public function getIncotermName()
    {
        return $this->incotermName;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedByName()
    {
        return $this->createdByName;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangedByName()
    {
        return $this->lastChangedByName;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalActiveRows()
    {
        return $this->totalActiveRows;
    }

    /**
     *
     * @return mixed
     */
    public function getMaxRowNumber()
    {
        return $this->maxRowNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getCompletedRows()
    {
        return $this->completedRows;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenAPAmount()
    {
        return $this->openAPAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrencyISO()
    {
        return $this->docCurrencyISO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrencyISO()
    {
        return $this->localCurrencyISO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrencyId()
    {
        return $this->docCurrencyId;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrencyId()
    {
        return $this->localCurrencyId;
    }

    /**
     *
     * @param mixed $pictureList
     */
    public function setPictureList($pictureList)
    {
        $this->pictureList = $pictureList;
    }

    /**
     *
     * @param mixed $attachmentList
     */
    public function setAttachmentList($attachmentList)
    {
        $this->attachmentList = $attachmentList;
    }

    /**
     *
     * @param mixed $totalPicture
     */
    public function setTotalPicture($totalPicture)
    {
        $this->totalPicture = $totalPicture;
    }

    /**
     *
     * @param mixed $totalAttachment
     */
    public function setTotalAttachment($totalAttachment)
    {
        $this->totalAttachment = $totalAttachment;
    }

    /**
     *
     * @param mixed $docRows
     */
    public function setDocRows($docRows)
    {
        $this->docRows = $docRows;
    }

    /**
     *
     * @param mixed $rowIdArray
     */
    public function setRowIdArray($rowIdArray)
    {
        $this->rowIdArray = $rowIdArray;
    }

    /**
     *
     * @param mixed $rowsOutput
     */
    public function setRowsOutput($rowsOutput)
    {
        $this->rowsOutput = $rowsOutput;
    }

    /**
     *
     * @param mixed $postingYear
     */
    public function setPostingYear($postingYear)
    {
        $this->postingYear = $postingYear;
    }

    /**
     *
     * @param mixed $postingMonth
     */
    public function setPostingMonth($postingMonth)
    {
        $this->postingMonth = $postingMonth;
    }

    /**
     *
     * @param mixed $docYear
     */
    public function setDocYear($docYear)
    {
        $this->docYear = $docYear;
    }

    /**
     *
     * @param mixed $docMonth
     */
    public function setDocMonth($docMonth)
    {
        $this->docMonth = $docMonth;
    }

    /**
     *
     * @param mixed $postingPeriodFrom
     */
    public function setPostingPeriodFrom($postingPeriodFrom)
    {
        $this->postingPeriodFrom = $postingPeriodFrom;
    }

    /**
     *
     * @param mixed $postingPeriodTo
     */
    public function setPostingPeriodTo($postingPeriodTo)
    {
        $this->postingPeriodTo = $postingPeriodTo;
    }

    /**
     *
     * @param mixed $postingPeriodId
     */
    public function setPostingPeriodId($postingPeriodId)
    {
        $this->postingPeriodId = $postingPeriodId;
    }

    /**
     *
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     *
     * @param mixed $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     *
     * @param mixed $companyToken
     */
    public function setCompanyToken($companyToken)
    {
        $this->companyToken = $companyToken;
    }

    /**
     *
     * @param mixed $companyCode
     */
    public function setCompanyCode($companyCode)
    {
        $this->companyCode = $companyCode;
    }

    /**
     *
     * @param mixed $vendorId
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    /**
     *
     * @param mixed $vendorToken
     */
    public function setVendorToken($vendorToken)
    {
        $this->vendorToken = $vendorToken;
    }

    /**
     *
     * @param mixed $vendorAddress
     */
    public function setVendorAddress($vendorAddress)
    {
        $this->vendorAddress = $vendorAddress;
    }

    /**
     *
     * @param mixed $vendorCountry
     */
    public function setVendorCountry($vendorCountry)
    {
        $this->vendorCountry = $vendorCountry;
    }

    /**
     *
     * @param mixed $completedAPRows
     */
    public function setCompletedAPRows($completedAPRows)
    {
        $this->completedAPRows = $completedAPRows;
    }

    /**
     *
     * @param mixed $completedGRRows
     */
    public function setCompletedGRRows($completedGRRows)
    {
        $this->completedGRRows = $completedGRRows;
    }

    /**
     *
     * @param mixed $paymentTermName
     */
    public function setPaymentTermName($paymentTermName)
    {
        $this->paymentTermName = $paymentTermName;
    }

    /**
     *
     * @param mixed $paymentTermCode
     */
    public function setPaymentTermCode($paymentTermCode)
    {
        $this->paymentTermCode = $paymentTermCode;
    }

    /**
     *
     * @param mixed $warehouseName
     */
    public function setWarehouseName($warehouseName)
    {
        $this->warehouseName = $warehouseName;
    }

    /**
     *
     * @param mixed $warehouseCode
     */
    public function setWarehouseCode($warehouseCode)
    {
        $this->warehouseCode = $warehouseCode;
    }

    /**
     *
     * @param mixed $paymentMethodName
     */
    public function setPaymentMethodName($paymentMethodName)
    {
        $this->paymentMethodName = $paymentMethodName;
    }

    /**
     *
     * @param mixed $paymentMethodCode
     */
    public function setPaymentMethodCode($paymentMethodCode)
    {
        $this->paymentMethodCode = $paymentMethodCode;
    }

    /**
     *
     * @param mixed $incotermCode
     */
    public function setIncotermCode($incotermCode)
    {
        $this->incotermCode = $incotermCode;
    }

    /**
     *
     * @param mixed $incotermName
     */
    public function setIncotermName($incotermName)
    {
        $this->incotermName = $incotermName;
    }

    /**
     *
     * @param mixed $createdByName
     */
    public function setCreatedByName($createdByName)
    {
        $this->createdByName = $createdByName;
    }

    /**
     *
     * @param mixed $lastChangedByName
     */
    public function setLastChangedByName($lastChangedByName)
    {
        $this->lastChangedByName = $lastChangedByName;
    }

    /**
     *
     * @param mixed $totalRows
     */
    public function setTotalRows($totalRows)
    {
        $this->totalRows = $totalRows;
    }

    /**
     *
     * @param mixed $totalActiveRows
     */
    public function setTotalActiveRows($totalActiveRows)
    {
        $this->totalActiveRows = $totalActiveRows;
    }

    /**
     *
     * @param mixed $maxRowNumber
     */
    public function setMaxRowNumber($maxRowNumber)
    {
        $this->maxRowNumber = $maxRowNumber;
    }

    /**
     *
     * @param mixed $netAmount
     */
    public function setNetAmount($netAmount)
    {
        $this->netAmount = $netAmount;
    }

    /**
     *
     * @param mixed $taxAmount
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }

    /**
     *
     * @param mixed $grossAmount
     */
    public function setGrossAmount($grossAmount)
    {
        $this->grossAmount = $grossAmount;
    }

    /**
     *
     * @param mixed $discountAmount
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     *
     * @param mixed $billedAmount
     */
    public function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
    }

    /**
     *
     * @param mixed $completedRows
     */
    public function setCompletedRows($completedRows)
    {
        $this->completedRows = $completedRows;
    }

    /**
     *
     * @param mixed $openAPAmount
     */
    public function setOpenAPAmount($openAPAmount)
    {
        $this->openAPAmount = $openAPAmount;
    }

    /**
     *
     * @param mixed $docCurrencyISO
     */
    public function setDocCurrencyISO($docCurrencyISO)
    {
        $this->docCurrencyISO = $docCurrencyISO;
    }

    /**
     *
     * @param mixed $localCurrencyISO
     */
    public function setLocalCurrencyISO($localCurrencyISO)
    {
        $this->localCurrencyISO = $localCurrencyISO;
    }

    /**
     *
     * @param mixed $docCurrencyId
     */
    public function setDocCurrencyId($docCurrencyId)
    {
        $this->docCurrencyId = $docCurrencyId;
    }

    /**
     *
     * @param mixed $localCurrencyId
     */
    public function setLocalCurrencyId($localCurrencyId)
    {
        $this->localCurrencyId = $localCurrencyId;
    }

    /**
     *
     * @return mixed
     */
    public function getLazyRowSnapshotCollectionReference()
    {
        return $this->lazyRowSnapshotCollectionReference;
    }

    /**
     *
     * @param mixed $lazyRowSnapshotCollection
     */
    public function setLazyRowSnapshotCollection($lazyRowSnapshotCollection)
    {
        $this->lazyRowSnapshotCollection = $lazyRowSnapshotCollection;
    }
}